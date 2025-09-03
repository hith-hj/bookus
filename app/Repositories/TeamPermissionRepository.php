<?php

namespace App\Repositories;

use App\Models\TeamPermission;
use App\Models\Center;
use App\Models\Contact;
use App\Models\Image;
use App\Models\OpenDay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class  TeamPermissionRepository
{

    public function add(Request $request)//: User
    {
        $teamPermission = new TeamPermission(populateModelData($request, TeamPermission::class));
        $teamPermission->save();
    }

    public  function getCategories( Request $request){
    $categories=TeamPermission::query();
    if ($search = $request->get('search')) {
        $categories->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');

        });}
        return $categories->latest();
    }
    public function getTeamPermission( $teamPermission) {
        $teamPermissionDetails=TeamPermission::query()->findOrFail($teamPermission);
        return $teamPermissionDetails;
    }
    public function update(Request $request,$teamPermission)  {
        $teamPermission->update(populateModelData($request, TeamPermission::class));
        $teamPermission->save();
    }

    public function delete(TeamPermission $teamPermission)
    {
        $teamPermission->delete();

    }
   public function getAllCategories()
   {
    return TeamPermission::query()->get();
   }

}
