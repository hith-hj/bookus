<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    private $AdminRepository;

    public function __construct(AdminRepository $AdminRepository)
    {
        $this->AdminRepository = $AdminRepository;
    }

    public function index(Request $request){
        $roles = Role::with(['permissions','users'])
        ->when($request->has('search'),function($query) use($request){
            $query->where([['name','like','%'.$request->search.'%']]);
        })->paginate(10);
        return view('admin.pages.roles',['roles'=>$roles]);
    }

    public function store(Request $request){
        $request->validate([
            'name'=>['required','unique:roles,name'],
        ]);
        $role = Role::create(['name'=>$request->name]);
        return redirect()->route('admin.roles')->with('success','Role created');
    }

    public function delete($id){
        $role = Role::with('users')->findOrFail($id);
        if(count($role->users)>0){
            return back()->with('error','Role can not be deleted');
        }
        $role->delete();
        return redirect()->route('admin.roles')->with('success','Role deleted');
    }

}