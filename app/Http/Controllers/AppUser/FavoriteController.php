<?php

namespace App\Http\Controllers\AppUser;
use App\Http\Controllers\Admin\Application;
use App\Http\Controllers\Admin\Factory;
use App\Http\Controllers\Admin\View;
use App\Models\Center;
use App\Models\Favorite;
use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
class FavoriteController extends ApiController
{

    public function listFavorite(Request $request)
    {
        $user=User::with("favorites")->find(auth('client')->id());
        return $this->respondSuccess([
            'favorites'=>$user->favorites
        ]);
    }
    
    public function addToFavorite(Request $request)
    {
        $center=Center::findOrFail($request->center_id);
        $user=User::findOrFail(auth('client')->id());
        if(Favorite::where([['center_id',$center->id],['user_id',$user->id]])->exists())
        {
            $user->favorites()->detach($center->id);
            $message = 'Favorite Removed';
        }else{
            $user->favorites()->attach($center->id);
            $message = 'Favorite Added';
        }
        return $this->respondSuccess($message);
    }

}
