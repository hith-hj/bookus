<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Requests\AdminRequest;
use App\Http\Requests\api\activateUserRequest;
use App\Models\Admin;
use App\Models\Notification;
use App\Models\User;
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
use Validator;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Exports\AdminsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use PDF;

class AuthController extends ApiController
{
    private $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function register(Request $request) : JsonResponse {
        $request->validate([
            'first_name' => ['required','string','between:2,50'],
            'last_name' => ['required','string','between:2,50'],
            'phone_number' => ['required','regex:/^[+][1-9]{1,4}[0-9]{6,9}$/','unique:users'],
            'password' => ['required','confirmed','string','min:6'],
            'email' => ['required','email','max:191','unique:users'],
            'firebase_token' => ['required',],
        ],['phone_number:regex'=>'phone number format is invalid']);
        $user = (new User($request->all()));
        $user->encryptPassword()->generateActivationCode()->save(); 
        
        $noti = (new Notification())->sendToTopic(
            'Activation',
            "Your activation code : $user->code",
            $user->firebase_token
        );
        // $noti = (new Notification())->sendSms($request->phone_number,"4bookus account activation code is $user->code");
        return $this->respondSuccess(['message' => 'User Registered','is_verified'=>false]);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone_number' => ['required','exists:users,phone_number'],
            'password' => ['required','string','min:6'],
            'firebase_token' => ['required'],
        ]);
        $user = User::where('phone_number',$request->phone_number)->first();
        if (!$user || !Hash::check($request->password, $user->password) || $user->status == -1){
            return $this->respondError(__('api.username_or_password_invalid'));
        }
        
        if (!$user->is_verified || is_null($user->mobile_verified_at)){
            return $this->respondError(__('api.You need to activate your account'));
        }
        
        if($user->firebase_token != $request->firebase_token){
            if($request->filled('clear_devices')){
                $user->firebase_token = $request->firebase_token;
                $user->tokens()->delete();
            }else{
                return $this->respondError(['message'=>'Log out from Other device','is_popup'=>true]);
            }
        }
        $user->token = $user->createToken('user_token')->plainTextToken;
        $user->save();
        return $this->respondSuccess(['message' => 'logged in','user' => $user,]);
    }
    
    public function activateUser(Request $request): JsonResponse
    {
        $request->validate([
            'code_type' => ['required','in:account_verification,password_reset'],
            'phone_number' => ['required','exists:users,phone_number'],
            'code' => ['required','digits:4'],
        ]);
        $user = User::wherePhoneNumber($request->get('phone_number'))->first();
        
        if(!in_array($request->get('code'),[$user->code,$user->reset_token])){
            return $this->respondError(__('api.code_not_valid'));
        }
        
        if($request->code_type == 'account_verification'){
            if($user->is_verified){
                return $this->respondError(__('api.already verified'));
            }
            if(!is_null($user->remember_token)){
                $user->remember_token = null;
            }
            $user->activateUser()->save();
        }elseif($request->code_type == 'password_reset'){
            $user->clearPasswordResetCode()->save();
        }else{
            return $this->respondError('Code verification went wrong');
        }
        return $this->respondSuccess('User Verified');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['phone_number' => ['required','exists:users,phone_number'],]);
        $user = User::wherePhoneNumber($request->get('phone_number'))->first();
        $user->passwordResetCode()->save();
        $noti = (new Notification())->sendToTopic(
                'Reset',
                "Your Password Reset code : $user->reset_token",
                $user->firebase_token
            );
        return $this->respondSuccess(['message' => 'Code is sent']);
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone_number' => ['required','exists:users,phone_number'],
            'new_password' => ['required','string','confirmed','min:6',],
        ]);
        $user= User::wherePhoneNumber($request->get('phone_number'))->first();
        $user->encryptPassword($request->get('new_password'))->save();
        return $this->respondSuccess(['message' => 'Password changed']);
    }

    public function changeNumber(Request $request)
    {
        $request->validate([
            'phone_number' => ['required','exists:users,phone_number',],
            'new_phone_number' => ['required','regex:/^[+][1-9]{1,4}[1-9]{9}$/',],
            'password' => ['required','string','min:6',],
        ]);
        $user = User::find(auth('client')->id());
        if(!$user || !Hash::check($request->password, $user->password)){
            return $this->respondError('User Not Found');
        }
        if(!$user->is_verified || is_null($user->mobile_verified_at)){
            return $this->respondError('You need to verify your account');
        }
        if($user->phone_number == $request->new_phone_number){
            return $this->respondError('Phone numbers already exists');
        }
        $user->is_verified=0;
        $user->mobile_verified_at=null;
        $user->remember_token = $user->phone_number;
        $user->phone_number=$request->get('new_phone_number');
        $user->generateActivationCode()->save();
        $noti = (new Notification())->sendToTopic(
                'Activation',
                "Your activation code is $user->code",
                $user->firebase_token
            );
        return $this->respondSuccess(['code'=> $user->code,]);
    }
    
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => ['required','string','min:6',],
            'new_password' => ['required','string','confirmed','min:6',],
        ]);
        $user= User::find(auth('client')->id());
        if(!$user){
            return $this->respondError('User not found ');
        }
        if(!Hash::check($request->get('old_password'), $user->password)){
            return $this->respondError(__('api.old_password_not_correct'));
        }
        $user->update(['password'=>bcrypt($request->get('new_password'))]);
        return $this->respondSuccess(__('api.change_password_successfully'));
    }
    
    public function resendCode(Request $request)
    {
        $request->validate([
            'phone_number' => ['required','exists:users,phone_number'],
        ]);
        $user = User::wherePhoneNumber($request->get('phone_number'))->first();
        $user->generateActivationCode()->save();
        $noti = (new Notification())->sendToTopic(
            'Activation',
            "Your activation code is $user->code",
            $user->firebase_token
        );
        return $this->respondSuccess(['message' =>__('api.sent_successfully_registered')]);
    }
    
    public function logout(){
        auth('client')->user()->update(['token'=>null]);
        auth('client')->user()->tokens()->delete();
        return $this->respondSuccess(['message'=>'logged out']);
    }
    
    public function delete(Request $request)
    {
        $user = User::find(auth('client')->id());
        if(is_null($user)){
            return $this->respondError('User Not found');
        }
        $user->update(['status'=>-1]);
        return $this->respondSuccess('User Deleted');
    }
    
    
    // SUB FUNCTIONS XXXXXXXXXXXXXXXXXXXXXXXXXXXX
    
    public function getAdmin(Request $request)
    {
        $limit = $request->get('limit') ?: 10;
        if ($limit > 100) $limit = 100;
        $admins = $this->adminRepository->getAdmins($request)->paginate($limit);
        return $this->respondSuccess($admins->all(), $this->createApiPaginator($admins));
    }

    public function getRoles(Request $request)
    {
        $roles=Role::query()->paginate(10);
        return $this->respondSuccess($roles->all(), $this->createApiPaginator($roles));
    }

    public function  getPermissions(Request $request)
    {
        $perm=Permission::query()->paginate(10);
        return $this->respondSuccess($perm->all(), $this->createApiPaginator($perm));
    }
    
    public function addRole(Request $request)
    {
        $items=json_decode($request->get('permissions'),true);
        $newRole = Role::create(['name' => $request->get('name')]);
        if($newRole){
            foreach($items as $item) {
                if($item['status'] == true)
                $newRole->givePermissionTo($item['name']);
            }
            return $this->respondSuccess();
        }else{
            return $this->respondError("not save have problem");
        }
    }

}
