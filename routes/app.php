<?php

use App\Http\Controllers\AppUser\AddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Category;
use App\Models\Center;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Http\Controllers\AppUser\AuthController;
use App\Http\Controllers\AppUser\CategoryController;
use App\Http\Controllers\AppUser\CenterController;
use App\Http\Controllers\AppUser\UserController;
use App\Http\Controllers\AppUser\AppointmentController;
use App\Http\Controllers\AppUser\FavoriteController;
use App\Http\Controllers\AppUser\HomeController;
use App\Http\Controllers\AppUser\ProfileController;
use App\Http\Controllers\AppUser\UserNotificationController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\FaqController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('lang')->prefix('user/')->name('user')->group(function () {
//USER APP AUTH
    Route::post('/change-number',[AuthController::class,'changeNumber'])->middleware('auth:client');
    Route::post('/change-password',[AuthController::class,'changePassword'])->middleware('auth:client');
    Route::delete('/del-self',[AuthController::class,'delete'])->middleware('auth:client');
    Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:client');
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/active-user',[AuthController::class,'activateUser']);
    Route::post('/forgot-password',[AuthController::class,'forgotPassword']);
    Route::post('/reset-password',[AuthController::class,'resetPassword']);
    Route::post('/resend-code',[AuthController::class,'resendCode']);
    Route::get('/polices',[PolicyController::class,'polices']);
    Route::get('/faqs',[FaqController::class,'faqs']);
});


Route::middleware(['auth:client','lang'])->prefix('user/')->name('user')->group(function () {
    //center information
    Route::get('/all-categories',[CategoryController::class,'allCategories']);
    Route::post('/centers-category',[CategoryController::class,'centersByCategory']);
    Route::post('/centers-details',[CenterController::class,'getCenterDetails']);

    //profile user
    Route::get('/profile-details',[UserController::class,'getUserDetails']);
    Route::post('/profile-edit',[UserController::class,'editUser']);
    
    //  process appointment
    Route::get('/get-categories-services-center',[AppointmentController::class,'getCenterCategoriesForCenter']);
    Route::get('/all-services',[AppointmentController::class,'allServices']);
    Route::post('/get-avaliable-shifts',[AppointmentController::class,'avaliableShifts']);
    Route::post('/check-appointment-date',[AppointmentController::class,'checkDate']);
    Route::post('/select-member',[AppointmentController::class,'selectMember']);
    Route::get('/member_info',[AppointmentController::class,'memberInfo']);
    Route::get('/appointment-details/{id}',[AppointmentController::class,'getAppointmentId']);
    Route::post('/review-appointment',[AppointmentController::class,'reviewBeforeConfirm']);
    Route::post('/new-appointment',[AppointmentController::class,'newAppointment']);
    
    //FAVORITE
    Route::post('/add-favorite',[FavoriteController::class,'addToFavorite']);
    Route::get('/list-favorite',[FavoriteController::class,'listFavorite']);
    
    //recommended
    Route::get('/recommended',[HomeController::class,'recommended']);
    Route::get('/featured',[HomeController::class,'featured']);
    Route::get('/offers',[HomeController::class,'offers']);
    Route::post('/add-review',[HomeController::class,'addReview']);
    
    //Gifts
    Route::post('/gifts',[AppointmentController::class,'allGigts']);
    
    //Membership
    Route::post('/memberships',[AppointmentController::class,'allMembership']);
    
    //orders
    Route::get('/get-appointment',[AppointmentController::class,'getAppointment']);
    Route::post('/edit-appointment',[AppointmentController::class,'editAppointment']);
    Route::post('/cancel-appointment',[AppointmentController::class,'cancelAppointment']);
    Route::get('/cancel-reason',[AppointmentController::class,'cancelReason']);

    //Profile
    Route::post('/profile-update',[ProfileController::class,'profileUpdate']);
    Route::get('/profile',[ProfileController::class,'getProfile']);
    Route::post('/delete-image',[ProfileController::class,'deleteImage']);
    
    //SETTING
    Route::apiResource('settings', 'App\Http\Controllers\Admin\SettingController');
    
    //Addresses
    Route::apiResource('addresses', 'App\Http\Controllers\AppUser\AddressController');
    Route::post('/addresses-default',[AddressController::class,'setDefault']);

    //search
    Route::post('/search',[HomeController::class,'search']);
    //vouchers
    Route::get('/vouchers',[HomeController::class,'vouchers']);

    //NOTIFICATIONS
    Route::post('/update-token', [UserNotificationController::class, 'updateFirebaseToken']);
    Route::get('/notifications',[UserNotificationController::class, 'userNotifications']);
    Route::post('/active-notifications',[UserNotificationController::class, 'activeNotifications']);

    //rate app
    Route::get('/get-rate-app',[UserController::class, 'getRateApp']);
    Route::post('/edit-rate-app',[UserController::class, 'editRateApp']);
    Route::post('/contact-us', [HomeController::class, 'contactUs'])->name('contactUs');

});
