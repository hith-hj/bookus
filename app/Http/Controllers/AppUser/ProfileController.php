<?php

namespace App\Http\Controllers\AppUser;
use App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends ApiController
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }
    public function getProfile(Request $request)
    {
     $user =   User::find(auth('client')->id());
        if (!$user){
            return $this->respondError('not_found_user');
        }
        $user->makeHidden(['address',
            'code',
            'mobile_verified_at',
            'reset_token','mobile_verified_at',
            'is_verified','status','firebase_token','reset_verified'
        ]);
    return $this->respondSuccess($user);
    }

    public function profileUpdate(Request $request)
    {
        $user =   User::find(auth('client')->id());
        if (!$user){
            return $this->respondError('not_found_user');
        }
        $this->userRepository->update($request, $user);
        return $this->respondSuccess('Profile Updated');
    }

    public function deleteImage()
    {
        $user =   User::find(auth('client')->id());

        if ($user->image != null) {
            $user->image = Storage::disk('public')->delete($user->image);
        }
        $user->image=null;
        $user->save();
        return $this->respondSuccess();
    }

}
