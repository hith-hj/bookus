<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class HomeController extends ApiController
{
    public function abilities()  {
        //get user
        $user = Admin::whereId(auth('sanctum')->id())->with('permissions')->first();
        $perm=$user->getPermissionsViaRoles()->pluck('name');


        $role=$user->roles->first()->name;
        return $this->respondSuccess([
            "user"=>$user,
            "permissions"=>$perm,
            "role" =>$role
        ]);
        // get permission user
        // return all
    }

}
