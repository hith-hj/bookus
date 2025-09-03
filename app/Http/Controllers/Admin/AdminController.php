<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminRequest;
use App\Models\Admin;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Repositories\AdminRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
// use Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Exports\AdminsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator ;
use PDF;
use DB;

class AdminController extends ApiController
{
    private $adminRepository;

    private const Deleted = -1;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'email' => 'required|string|email:rfc,dns|unique:admins',
            'password' => 'required|string|min:6',
            'is_admin'=>'sometimes|in:1,0',
            'firebase_token' => 'sometimes|required|string',
        ]);
        if ($validator->fails()) {
            return $this->respondError($validator->errors()->first(), $validator->errors());
        }
        $admin = new Admin($request->except(['password']));
        $admin->password = bcrypt($request->password);
        $admin->center_position = $request->role == 'center' ? 'owner' : 'member';
        if ($request->hasFile('cover_image')){
            $admin->cover_image = Storage::disk('public')->put('admins', $request->file('cover_image'));
        }
        $admin->save();
        $admin->update(['token'=>$admin->createToken('Admin_Token')->plainTextToken]);
        if($request->role != 'member' && in_array($request->role,['center'])){
            $admin->assignRole($request->role);
        }
        $admin = Admin::where('email',$request->email)->first();
        if(isset($admin->firebase_token) && $admin->active_notification ==1 ){
            $noti = Notification::create([
                'title' =>  'Success',
                'message' =>  'Admin egister',
                'appointment_id' =>  $admin->id,
                'user_id' =>  $admin->id,
                'type' =>  'Register',
            ]);
            $res = $noti->sendToTopic("Account created", "New admin is registered", $admin->firebase_token, 'Register', $admin->email);            
        }
        return $this->respondSuccess(['message' => 'User registered','admin' => $admin,]);
    }

    public function login(Request $request): JsonResponse
    {
        $admin = Admin::with('center')->where('email', $request->email)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return $this->respondError(['message' => __('api.username_or_password_invalid'), 'code' => '']);
        }
        
        if ($admin->status == self::Deleted) {
            return $this->respondError(['message' => 'Account is Deleted', 'code' => -1]);
        }

        if($request->filled('firebase_token')){
            if(!is_null($admin->firebase_token) && $admin->firebase_token != $request->firebase_token){
                if($request->filled('clear_devices') && $request->clear_devices == true){
                    $admin->firebase_token = $request->firebase_token; 
                }else{
                    return $this->respondError(['message'=>'Log out from other device','code'=> 5]);
                }
            }
        }
        
        $admin->tokens()->delete();
        $admin->token = $admin->createToken('API')->plainTextToken;
        $admin->save();
        $admin->makeHidden(['roles','permissions']);
        $arrayPermission = [];
        $groupedPermissions = [];
        if($admin->center_position != 'member')
        {
            foreach ($admin->getAllPermissions() as $p) {
                array_push($arrayPermission, $p->name);
                $permission = explode(' ',$p->name);
                if(array_key_exists($permission[1],$groupedPermissions))
                {
                    array_push($groupedPermissions[$permission[1]] , $permission[0]);
                }else{
                    $groupedPermissions[$permission[1]] = [$permission[0]];
                }
            }
        }
        $admin->permission = $groupedPermissions;
        return $this->respondSuccess( 
            [
                'message' => 'user logged in',
                'token' => $admin->token,
                'user' => $admin,
                'permission' => $arrayPermission
            ]
        );
    }

    public function getAdmin(Request $request)
    {
        $limit = $request->get('limit') ?: 10;
        if ($limit > 100) $limit = 100;
        $admins = $this->adminRepository
            ->getAdmins($request)
            ->paginate($limit);

        return $this->respondSuccess($admins->all(), $this->createApiPaginator($admins));
    }

    public function getRoles(Request $request)
    {
        $roles = Role::query()->paginate(10);
        return $this->respondSuccess($roles->all(), $this->createApiPaginator($roles));
    }


    public function  getPermissions(Request $request)
    {
        $perm = Permission::query()->paginate(10);
        return $this->respondSuccess($perm->all(), $this->createApiPaginator($perm));
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */

    public function addRole(Request $request)
    {
        $perm = $request->get('permissions');
        $roleName = $request->get('name');
        $items = json_decode($perm, true);
        $newRole =   Role::create(['name' => $roleName]);
        if ($newRole) {
            foreach ($items as $item) {
                if ($item['status'] == true)
                    $newRole->givePermissionTo($item['name']);
            }
            return $this->respondSuccess();
        } else {
            return $this->respondError("not save have problem");
        }
    } 

    public function deleteAdmin($id)
    {
        $admin = Admin::find($id);
        if (!is_null($admin)) {
            $this->adminRepository->delete($admin);
            return response()->json([
                'result' => 'success',
                'message' => 'Admin deleted.'
            ], 200);
        }
        return response()->json([
            'result' => 'failed',
            'message' => 'brand not found',
        ], 500);
    }

    public function detailsAdmin()
    {
        $admin = auth('admin')->user();
        return !$admin ? $this->respondError(['admin'=>'account is not found']) : $this->respondSuccess(['admin' => $admin]);
    }

    
    public function editAdmin(Request $request)
    {
        $admin=auth('admin')->user();
        if(!$admin){
            return $this->respondError(['message'=>'Admin not found']);
        }
            
        $validator = Validator::make($request->all(), [
            'first_name'=>['required','string',],
            'last_name'=>['required','string',],
            'phone_number'=>['regex:/^[+]{1}[1-9]{1}[0-9]{0,2}[-][1-9]{6,9}$/',],
            'aaddress'=>['string','max:100'],
            'cover_image'=>['file','image','between:2,512',],
        ]);
        if($validator->fails()){
            return $this->respondError($validator->errors()->first(),$validator->errors());
        }
            
        $newAdmin=$this->adminRepository->update($request, $admin);

        if($request->has('role') && $request->role != null){
            $admin->syncRoles((int)$request->role);
        }
        return $this->respondSuccess(['message'=>'Profile Edited']);
    }


    public function changeAdminPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'=>['required','string','min:6',],
            'new_password'=>['required','string','min:6','confirmed'],
        ]);
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'payload'=>$validator->errors()]);
        }
        $admin = auth('admin')->user();
        if (!Hash::check($request->old_password,$admin->password)) {
            return $this->respondError(['message' => 'the old password is wrong']);
        }
        if (!$request->filled('new_password')) {
            return $this->respondError(['message' => 'please enter a new password']);
        }
        $admin->password = bcrypt($request->new_password);
        $admin->save();
        return $this->respondSuccess(['message'=>'Password is Changed']);
    }

    public function logout()
    { 
        auth('admin')->user()->update(['token' => null]);
        auth('admin')->user()->tokens()->delete();
        return $this->respondSuccess(['message' => 'you logged out']);
    }

    public function forgetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc,dns'],
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ]);
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return $this->respondError(['message' => 'there is no user with this Email']);
        }
        $admin->password = bcrypt($request->password);
        $admin->save();
        return $this->login($request);
    }

    public function deleteAccount()
    {
        $admin = auth('admin')->user();
        if (!$admin || $admin->status == self::Deleted) {
            return $this->respondError(['message' => 'No Account found']);
        }
        $admin->status = -1;
        $admin->save();
        $admin->tokens()->delete();
        return $this->respondSuccess(['message' => 'Account Deleted Successfuly']);
    }

    public function activateAccount(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc,dns',],
            'password' => ['required', 'string', 'min:6',]
        ]);
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return $this->respondError(['message' => 'No Account found']);
        }
        if ($admin->status != self::Deleted) {
            return $this->respondError(['message' => 'your account is activated']);
        }
        $admin->status = 1;
        $admin->save();
        return $this->login($request);
    }

    public function  exportExcel(Request $request)
    {
        Excel::store(new AdminsExport($request->search), 'adminExcel/admins.xlsx', 'public', \Maatwebsite\Excel\Excel::XLSX);
        return response()->download(public_path('storage/adminExcel/admins.xlsx'));
    }
    public function downloadPDF(Request $request)
    {
        $users = Admin::query();
        $search = $request->search;
        if ($search) {
            $users->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        $users =  $users->latest()->get();
        $pdf = PDF::loadView('pdf.adminPdf', array('users' =>  $users))->save('storage/adminPdf/admins.pdf');
        return response()->download(public_path('storage/adminPdf/admins.pdf'));
    }
}
