<?php

namespace App\Http\Controllers\AppUser;
use App\Http\Controllers\Admin\Application;
use App\Http\Controllers\Admin\Factory;
use App\Http\Controllers\Admin\View;
use App\Models\User;
use App\Models\Center;
use App\Repositories\UserRepository;
use App\Repositories\CenterRepository;
use App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
//CategoryController
class UserController extends ApiController
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {


    }
    public  function update(Request $request,User $user)
    {

    }
    public function show( $id)
    {

    }
    public function destroy(Request $request, User $user)
    {


    }

    public function getUserDetails(Request $request)
    {
      $user = User::find(auth('client')->id());
      return $this->respondSuccess([
          'profile' =>$user
      ]);
    }
    public function editUser(Request $request)
    {
        $user = User::find(auth('client')->id());
        $this->userRepository->update($request, $user);
        return response()->json([
            'result' => 'success',
        ], 200);
    }
    //rate app
    public function getRateApp(Request $request)
    {
        $user = User::find(auth('client')->id());
        return $this->respondSuccess([
           'rate'=>(double)$user->rate_app,
           'review'=>$user->review_app,
        ]);
    }
    
    public function editRateApp(Request $request)
    {
        $request->validate([
            'rate_app' => ['required','numeric','min:1'],
            'review_app' => ['nullable','string'],
        ]);
        $user = User::find(auth('client')->id());
        if($user->rate_app > 0){
            return $this->respondError(['message'=>'you can\'t rate app again']);
        }
        $user->update([
            'rate_app'=>$request->rate_app,
            'review_app'=>$request->review_app,
        ]);
        return $this->respondSuccess();
    }
}
