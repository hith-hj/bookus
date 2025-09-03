<?php
namespace App\Http\Controllers\AppUser;
use App\Http\Controllers\ApiController;
use App\Http\Requests\API\ActivateUserRequest;
//use App\Http\Requests\API\LoginUserRequest;
//use App\Http\Requests\API\ResetPasswordConfirmRequest;
//use App\Http\Requests\API\ResetPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Models\Notification;
use App\Models\User;
//use App\Repositories\RequestRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserNotificationController extends ApiController
{

    public function updateFirebaseToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'firebase_token' => 'required'
        ]);
        if ($validator->fails()) {
            Log::error($validator->errors());
            return $this->respondError($validator->errors()->first(), $validator->errors()->getMessages());
        }

        $user = $this->getUser($request);
        if (!$user)
            return $this->respondError(__('api.user_not_found'));
        if($request->get('firebase_token')=='0')
        {   
            $user->setFirebaseToken(null)->save();
        }else{
            $user->setFirebaseToken($request->get('firebase_token'))->save();
        }
        $not =new Notification();
        if ($user->active_notification)
            $not->sendToTopic("firebase token", "hello update firebase success", $user->firebase_token);
        return $this->respondSuccess();
    }

    public function userNotifications(Request $request): JsonResponse
    {
        $limit = $request->get('limit') ? : 10 ;
        if($limit > 30 ) $limit =30 ;

        $user = $this->getUser($request);
        if (!$user)
            return $this->respondError(__('api.user_not_found'));

        $notifications = $user->userNotifications()->latest()->paginate($limit);

        return $this->respondSuccess($notifications->all(), $this->createApiPaginator($notifications));
    }

    public function changeLang(Request $request): JsonResponse
    {
        $request->validate([
            'lang' => 'required|in:ar,en'
        ]);

        $user = $this->getUser($request);
        if (!$user)
            return $this->respondError(__('api.user_not_found'));

        $user->changeLang($request->get('lang'));
        $user->save();

        return $this->respondSuccess($user);
    }

    public function changeNotificationStatus(Request $request): JsonResponse
    {
        $user = $this->getUser($request);
        if (!$user)
            return $this->respondError(__('api.user_not_found'));

        $user->toggleNotificationStatus()->save();
        return $this->respondSuccess([
            'notification-status' => $user->app_notification_status
        ]);
    }

    public function deleteNotification($id): JsonResponse
    {
        Notification::query()->findOrFail($id)->delete();
        return $this->respondSuccess('success');
    }

    public function lastNotification(Request $request): JsonResponse
    {
        $user = $this->getUser($request);
        if (!$user)
            return $this->respondError(__('api.user_not_found'));

        $notification = $user->userNotifications()->orderBy('created_at', 'desc')->first();
        return $this->respondSuccess($notification);
    }

    public function activeNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            Log::error($validator->errors());
            return $this->respondError($validator->errors()->first(), $validator->errors()->getMessages());
        }
      $user=  User::find(auth('client')->id());
        $status=$request->get('status');
        $user->active_notification=$status;
        $user->save();
        return $this->respondSuccess();

}


}
