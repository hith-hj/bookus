<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    private $AdminRepository;

    public function __construct(AdminRepository $AdminRepository)
    {
        $this->AdminRepository = $AdminRepository;
    }

    public function index(Request $request){
        $admins = Admin::when($request->has('search'),function($query) use($request){
            $query->where('first_name','like','%'.$request->search.'%')
            ->orWhere('last_name','like','%'.$request->search.'%');
        })->when($request->has('filter'),function($query) use($request){
            if(in_array($request->filter,['active','inactive','owner','member'])){
                switch ($request->filter) {
                    case 'active':
                        $query->where('status',1);                        
                        break;
                    case 'inactive':
                        $query->where('status',0);                        
                        break;
                    case 'owner':
                        $query->where([['center_position','owner'],['is_admin',1]]);                        
                        break;
                    case 'member':
                        $query->where([['center_position','member'],['is_admin',0]]);                        
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
        })->paginate(8);
        $roles = Role::get();
        return view('admin.pages.admins',['admins'=>$admins,'roles'=>$roles]);
    }

    public function admin(Request $request, $id){
        $admin = Admin::with(['center:id,name','appointments'])->findOrFail($id);
        return view('admin.pages.admin',['admin'=>$admin,'roles'=>Role::get()]);
    }

    public function store(Request $request){
        $request->validate([
            'first_name'=>['required','string','max:20'],
            'last_name'=>['required','string','max:20'],
            'email'=>['required','email','unique:admins,email'],
            'phone_number'=>['required','regex:/^[+][1-9]{1,4}[0-9]{6,9}$/',],
            'password'=>['required','string','min:6'],
            'role'=>['required','exists:roles,id'],
        ]);
        $admin = Admin::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'phone_number'=>$request->phone_number,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'cover_image'=>'team/none.jpg',
            'status'=>0,
            'is_admin'=>0,
        ]);
        $role = Role::find($request->role);
        $admin->assignRole($role);
        return redirect()->back()->with('success','Admin Created');
    }

    public function edit(Request $request,$id){
        $request->validate([
            'first_name'=>['required','string','max:20'],
            'last_name'=>['required','string','max:20'],
            'email'=>['required','email'],
            'phone_number'=>['required','regex:/^[+][1-9]{1,4}[0-9]{6,9}$/',],
            'member_desc'=>['required','string','max:100'],
            'role'=>['required','exists:roles,id'],
        ]);
        $admin = Admin::findOrFail($id);
        if($admin->email != $request->email && Admin::where('email',$request->email)->count() > 0){
            return back()->with('error','Email already taken');
        }

        if($admin->roles()->count() > 0){
            $oldRole = Role::find($admin->roles()->first()->id);
            if($oldRole->id != $request->role){
                $admin->removeRole($oldRole);
                $newRole = Role::find($request->role);
                $admin->assignRole($newRole);
            }
        }else{
            $admin->assignRole(Role::find($request->role));
        }
        
        $admin->update([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            'phone_number'=>$request->phone_number,
            'member_desc'=>$request->member_desc,
        ]);
        return redirect()->route('admin.admin',$admin->id)->with('success','Admin Edited');
    }

    public function delete(Request $request, $id){
        Admin::findOrFail($id)->delete();
        return redirect()->route('admin.admins')->with('success','Admin deleted');
    }
}