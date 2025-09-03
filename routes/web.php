<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\HomeController as UserHome;
use App\Http\Controllers\User\ProfileController as UserProfile;
use App\Http\Controllers\User\CenterController as UserCenter;

use App\Http\Controllers\Admin\Web\HomeController as AdminHome;
use App\Http\Controllers\Admin\Web\AuthController as AdminAuth;
use App\Http\Controllers\Admin\Web\CenterController as AdminCenter;
use App\Http\Controllers\Admin\Web\CategoryController as AdminCategory;
use App\Http\Controllers\Admin\Web\ServiceController as AdminService;
use App\Http\Controllers\Admin\Web\AdminController as AdminAdmins;
use App\Http\Controllers\Admin\Web\RoleController as AdminRole;
use App\Http\Controllers\Admin\Web\ClientController as AdminClient;
use App\Http\Controllers\Admin\Web\AppointmentController as AdminAppointment;
use App\Http\Controllers\Admin\Web\ChartController as AdminChart;
use App\Http\Controllers\Admin\Web\RequestController as AdminRequest;
use App\Http\Controllers\Admin\Web\SettingController as AdminSetting;


Route::get('/',[HomeController::class , 'index']);
Route::get('/about',[HomeController::class , 'about'])->name('about');
Route::get('/search',[HomeController::class, 'search'])->name('search');
Route::get('/center',[HomeController::class, 'center'])->name('center');

Route::group(['prefix' => 'user',],function(){
    Route::get('/login',[AuthController::class, 'loginPage'])->name('user.loginPage');
    Route::post('/login',[AuthController::class, 'login'])->name('user.login');
    Route::get('/register',[AuthController::class, 'registerPage'])->name('user.registerPage');
    Route::post('/register',[AuthController::class, 'register'])->name('user.register');
    Route::group(['middleware'=>'auth:web'],function(){
        Route::post('/logout',[AuthController::class, 'logout'])->name('user.logout');
        Route::get('/home',[UserHome::class , 'home'])->name('user.home');
        Route::get('/profile',[UserProfile::class , 'profile'])->name('user.profile');
        Route::post('/profile/edit',[UserProfile::class , 'edit'])->name('user.profile.edit');
        Route::get('/appointments',[UserHome::class , 'appointments'])->name('user.appointments');
        Route::get('/favorites',[UserHome::class , 'favorites'])->name('user.favorites');
        Route::group(['prefix' => 'center',],function(){
            Route::get('/{id}',[UserCenter::class , 'center'])->name('user.center');
            Route::get('/toggleFavorite/{id}',[UserCenter::class , 'toggleFavorite'])->name('user.center.toggleFavorite');
        });
    });
});

Route::get('lang/{lang}', function($lang){
        if(in_array($lang,['en','ar'])){
            session()->put('lang',$lang);
            app()->setLocale($lang);
        }
        return redirect()->back();
    })->name('lang');
Route::group(['prefix' => 'adminx','middleware'=>'lang'],function(){
    Route::get('/login',[AdminAuth::class,'loginPage'])->name('admin.loginPage');
    Route::post('/login',[AdminAuth::class,'login'])->name('admin.login');
    Route::group(['middleware'=>'auth:admins',],function(){
        Route::post('/logout',[AdminAuth::class,'logout'])->name('admin.logout');
        Route::get('/home',[AdminHome::class,'home'])->name('admin.home');
        Route::group(['prefix'=>'centers'],function(){
            Route::get('/',[AdminCenter::class,'index'])->name('admin.centers');
            Route::get('/{id}',[AdminCenter::class,'center'])->name('admin.center');
            Route::post('/block/{id}',[AdminCenter::class,'block'])->name('admin.center.block');
            Route::post('/unblock/{id}',[AdminCenter::class,'unblock'])->name('admin.center.unblock');
            Route::post('/store',[AdminCenter::class,'store'])->name('admin.center.store');
            Route::post('/edit/{id}',[AdminCenter::class,'edit'])->name('admin.center.edit');
            Route::post('/delete/{id}',[AdminCenter::class,'delete'])->name('admin.center.delete');
        });
        Route::group(['prefix'=>'categories'],function(){
            Route::get('/',[AdminCategory::class,'index'])->name('admin.categories');
            Route::get('/{id}',[AdminCategory::class,'category'])->name('admin.category');
            Route::post('/store',[AdminCategory::class,'store'])->name('admin.category.store');
            Route::post('/delete/{id}',[AdminCategory::class,'delete'])->name('admin.category.delete');
            Route::get('toggleCategory/{id}',[AdminCategory::class,'toggleCategory'])->name('admin.toggleCategory');
        });
        Route::group(['prefix'=>'services'],function(){
            Route::get('/',[AdminService::class,'index'])->name('admin.services');
        });
        Route::group(['prefix'=>'admins'],function(){
            Route::get('/',[AdminAdmins::class,'index'])->name('admin.admins');
            Route::get('/{id}',[AdminAdmins::class,'admin'])->name('admin.admin');
            Route::post('/store',[AdminAdmins::class,'store'])->name('admin.admin.store');
            Route::post('/edit/{id}',[AdminAdmins::class,'edit'])->name('admin.admin.edit');
            Route::post('/delete/{id}',[AdminAdmins::class,'delete'])->name('admin.admin.delete');
        });
        Route::group(['prefix'=>'roles'],function(){
            Route::get('/',[AdminRole::class,'index'])->name('admin.roles');
            Route::post('/store',[AdminRole::class,'store'])->name('admin.role.store');
            Route::post('/delete/{id}',[AdminRole::class,'delete'])->name('admin.role.delete');
        });
        Route::group(['prefix'=>'clients'],function(){
            Route::get('/',[AdminClient::class,'index'])->name('admin.clients');
            Route::get('/{id}',[AdminClient::class,'client'])->name('admin.client');
            Route::post('/store',[AdminClient::class,'store'])->name('admin.client.store');
            Route::post('/edit/{id}',[AdminClient::class,'edit'])->name('admin.client.edit');
            Route::post('/delete/{id}',[AdminClient::class,'delete'])->name('admin.client.delete');
        });
        Route::group(['prefix'=>'appointments'],function(){
            Route::get('/',[AdminAppointment::class,'index'])->name('admin.appointments');
        });
        Route::group(['prefix'=>'charts'],function(){
            Route::get('/',[AdminChart::class,'index'])->name('admin.charts');
        });
        Route::group(['prefix'=>'requests'],function(){
            Route::get('/',[AdminRequest::class,'index'])->name('admin.requests');
        });
        Route::group(['prefix'=>'settings'],function(){
            Route::get('/',[AdminSetting::class,'index'])->name('admin.settings');
            Route::post('/update',[AdminSetting::class,'update'])->name('admin.settings.update');
        });
    });
});

// Route::get('/{any}', [ApplicationController::class, 'applicationDashboard'])->where('any', '.*');