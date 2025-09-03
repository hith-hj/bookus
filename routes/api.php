<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
 use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CenterCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CenterServiceController;
 use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\CenterController;
use App\Http\Controllers\Admin\TeamPermissionController;
use App\Http\Controllers\Center\CenterAdminController;
use App\Http\Controllers\Center\CenterAppointmentsController;
use App\Http\Controllers\Center\CenterClientsController;
use App\Http\Controllers\Center\CenterReportsController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\FaqController;
use App\Models\Category;
use App\Models\Center;
use App\Models\CenterCategory;
use App\Models\CenterService;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//    Route::get('login', [AdminController::class, 'login']);
//

Route::prefix('admin')->group(function () {

    Route::prefix('center')->middleware('auth:sanctum')
        ->group(function(){
            Route::controller(CenterAdminController::class)->group(function (){
                Route::get('/get-details/{center}','centerDetails');
                Route::post('/create-center','createCenter');
                Route::post('/edit-center/{center}','editCenter');
                Route::delete('/delete-center/{center}','deleteCenter');
                Route::post('/request-center-activation','requestCenterActivation');
                Route::post('/request-center-verification','requestCenterVerification');
                
                Route::get('/get-center-days/{center_id}','getCenterDays');
                Route::post('/create-center-days', 'createCenterDays');
                Route::post('/delete-center-day', 'deleteCenterDay');
    
                Route::get('/center-available-contacts/{center_id}','centerAvailableContacts');
                Route::get('/get-center-contacts/{center_id}','getCenterContacts');
                Route::post('/create-center-contact', 'createCenterContact');
                Route::post('/delete-center-contact', 'deleteCenterContact');
    
                Route::get('/get-categories','getCategories'); 
                Route::get('/get-center-categories/{center_id}', 'getCenterCategories'); 
                Route::post('/create-center-categories', 'createCenterCategories'); 
                Route::post('/delete-center-categories', 'deleteCenterCategories'); 
    
                Route::get('/get-services', 'getServices'); 
                Route::get('/get-center-services/{center_id}', 'getCenterServices'); 
                Route::post('/create-center-services', 'createCenterServices'); 
                Route::post('/delete-center-services', 'deleteCenterServices'); 
                
                Route::get('/get-center-members','getCenterMembers');
                Route::post('/create-center-member','createCenterMember');
                Route::post('/delete-center-member','deleteCenterMember');
                
                Route::get('/get-center-offers','getCenterOffers');
                Route::get('/deactivate-center-offer','deactivateCenterOffer');
                Route::post('/create-center-offer','createCenterOffer');
                Route::post('/delete-center-offer','deleteCenterOffer');
                
                Route::get('/get-center-branches','getCenterBranches');
                Route::post('/create-center-branch','createCenterBranch');
                Route::post('/edit-center-branch','editCenterBranch');
                Route::post('/delete-center-branch','deleteCenterBranch');
                
                Route::get('get-center-holidays','getCenterHolidays');
                Route::post('create-center-holiday','createCenterHoliday');
                Route::post('delete-center-holiday','deleteCenterHoliday');
                // Route::get('get-members-holidays','getMembersHolidays');
                Route::post('create-member-holiday','createMemberholiday');
                Route::post('delete-member-holiday','deleteMemberholiday');
                
                Route::post('add-member-service','addMemberService');
                Route::post('delete-member-service','deleteMemberService');
                
                Route::post('add-member-day','addMemberDay');
                Route::post('delete-member-day','deleteMemberDay');
                
                Route::get('get-center-resources','getCenterResources');
                Route::post('create-center-resource','createCenterResource');
                Route::post('delete-center-resource','deleteCenterResource');
                
                Route::get('get-center-daysoff','getCenterDaysoff');
                Route::post('create-center-dayoff','createCenterDayoff');
                Route::post('delete-center-dayoff','deleteCenterDayoff');
                
                Route::get('get-center-cancelation-reasons','getCenterCancelationReasons');
                Route::post('create-center-cancelation-reason','createCenterCancelationReason');
                Route::post('delete-center-cancelation-reason','deleteCenterCancelationReason');
                
                Route::get('get-center-reminders','getCenterReminders');
                Route::post('create-center-reminder','createCenterReminder');
                Route::post('update-center-reminder','updateCenterReminder');
                Route::post('delete-center-reminder','deleteCenterReminder');
                Route::post('toggle-center-reminder','toggleCenterReminder');
                
                Route::get('get-center-settings','getCenterSettings');
                Route::post('set-center-settings','setCenterSettings');
                
                Route::get('get-center-notifications','getCenterNotifications');
                Route::post('set-center-notification-seen','setCenterNotificationSeen');
            });
            
            Route::controller(CenterAppointmentsController::class)->group(function (){
               Route::get('/center-appointments','getCenterAppointments');
               Route::post('/create-center-appointments','createCenterAppointment');
               Route::post('/edit-center-appointment','editCenterAppointment');
               Route::get('/cancel-center-appointments','cancelCenterAppointment');
               Route::get('/complete-center-appointments','completeCenterAppointment');
            });
            Route::controller(CenterClientsController::class)->group(function (){
                Route::post('check-user-exists','checkUserExists');
                
                Route::get('/get-center-clients','getCenterClients');
                Route::get('/get-client-details','getClientDetails');
                Route::post('/create-center-client','createCenterClient');
                
                Route::post('/edit-center-client','editCenterClient');
                Route::post('/delete-center-client','deleteCenterClient');
                
                Route::get('/get-center-note','getCenterNote');
                Route::post('/create-client-note','createCenterClientNote');
                Route::get('/delete-client-note','deleteCenterClientNote');
                
                Route::get('/get-center-memberships','getCenterMemberships');
                Route::post('/create-center-membership','createCenterMembership');
                Route::post('/cancel-center-membership','cancelCenterMembership');
                Route::post('/create-client-membership','createClientMembership');
                // Route::post('/cancel-client-membership','cancelClientMembership');
                
                Route::get('/get-center-gifts','getCenterGifts');
                Route::post('/create-client-gift','createClientGift');
                Route::post('/cancel-client-gift','cancelClientGift');
            });
            Route::controller(CenterReportsController::class)->group(function (){
                Route::get('/sales','salesIndex');
                Route::get('/sales/appointments','salesAppointments');
                Route::get('/sales/gifts','salesGifts');
                Route::get('/appointments','appointmentsIndex');
                Route::get('/dashboard','getCenterDashboard');
                Route::get('/get-center-transactions','getCenterTransactions');
                Route::get('/get-center-finances','getCenterFinances');
                Route::get('/get-center-discounts','getCenterDiscounts');
                Route::get('/get-center-sales','getCenterSales');
            });
    });


    //ADMIN ROUTING
    Route::post('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/register', [AdminController::class, 'register']);
    Route::post('/forget-password',[AdminController::class, 'forgetPassword']);
    Route::post('/activate-account', [AdminController::class, 'activateAccount']);
    
    Route::middleware('auth:sanctum')->group(function () {
        // Route::get('/details-admin/{id}', [AdminController::class, 'detailsAdmin']);
        Route::get('/details-admin', [AdminController::class, 'detailsAdmin']);
        Route::post('/edit-admin', [AdminController::class, 'editAdmin']);
        Route::get('/getAdmins', [AdminController::class, 'getAdmin']);
        Route::get('/roles', [AdminController::class, 'getRoles']);
        Route::get('/get-permissions', [AdminController::class, 'getPermissions']);
        Route::post('/new-role', [AdminController::class, 'addRole']);
        Route::post('/exportExcel', [AdminController::class, 'exportExcel']);
        Route::post('/exportPdf', [AdminController::class, 'downloadPDF']);
        Route::get('/logout',[AdminController::class,'logout']);
        Route::post('/change-admin-password', [AdminController::class, 'changeAdminPassword']);
        Route::get('/delete-admin/{id}', [AdminController::class, 'deleteAdmin']);
        Route::delete('/delete-account',[AdminController::class, 'deleteAccount']);
    });
    

    //USER ROUTING
    Route::apiResource('users', 'App\Http\Controllers\Admin\UserController');
    Route::post('/user_exporte_excel',[UserController::class,'userExportExcel']);
    Route::post('/user_export_pdf',[UserController::class,'userDownloadPDF']);

    //CENTER
    Route::apiResource('centers', 'App\Http\Controllers\Admin\CenterController');

    // add_contact_to_center
    Route::post('/add_contact',[CenterController::class,'addContact']);
    Route::post('/center_exporte_excel',[CenterController::class,'centerExportExcel']);
    Route::post('/center_export_pdf',[CenterController::class,'centerDownloadPDF']);
    //Category
    Route::apiResource('categories', 'App\Http\Controllers\Admin\CategoryController')->middleware("auth:sanctum");
    //services
    Route::apiResource('services', 'App\Http\Controllers\Admin\ServiceController')->middleware("auth:sanctum");
    // allCategories
    Route::get('/get_all_categories',[CategoryController::class,'allCategories']);
    //get all appointmen
    Route::get('/appointments',[AppointmentController::class,'allAppointments']);
    Route::get('/appointments_app',[AppointmentController::class,'appointmentCalendar']);
    Route::get('/get-center-categories-admin/{id}',[AppointmentController::class,'getCenterCategoriesForAdmin']);
    Route::get('/cal_date',[AppointmentController::class,'calDate']);
    Route::post('/new-appointment',[AppointmentController::class,'newAppointment']);
    Route::post('/get-avaliable-shifts',[AppointmentController::class,'avaliableShifts']);
    Route::get('/test',[AppointmentController::class,'getTotalDuration']);
    
    
    
    Route::get('abilities', [HomeController::class, 'abilities']);


    // center Dashboard
    Route::get('/get-center-categories/{id}',[AppointmentController::class,'getCenterCategories']);
    Route::get('/center_categories',[CenterCategoryController::class,'getAllCenterCategory']);
    Route::get('/center_category/{id}',[CenterCategoryController::class,'getCenterCategory']);
    Route::get('/center_treatment',[CenterCategoryController::class,'getTreatment']);
    Route::post('/new-center-services',[CenterServiceController::class,'newCenterServices']);
    Route::post('/new-center-category',[CenterCategoryController::class,'newCenterCategory']);
    Route::post('/edit-center-category/{id}',[CenterCategoryController::class,'update']);

    Route::delete('center-service/{id}', [CenterServiceController::class,'delete']);
    //members (center employee)
    Route::get('/members',[CenterController::class,'getMembers']);
    Route::post('/new-member',[CenterController::class,'newMember']);
    //contacts (center contacts: Ex phone number ,email, fb ,whatsapp )
    Route::get('/contacts',[CenterController::class,'getContact']);
    // new-contact

    Route::post('/new-contact',[CenterController::class,'newContact']);
    Route::delete('/delete-contact/{id}',[CenterController::class,'delContact']);
    Route::post('/update-center-days',[CenterController::class,'updateCenterDays']);
    Route::get('/get-center',[CenterController::class,'getCenterDetails']);
    Route::get('/get-center-categories-center',[AppointmentController::class,'getCenterCategoriesForCenter']);
    Route::apiResource('settings', 'App\Http\Controllers\Admin\SettingController');
    Route::post('/update-setting',[SettingController::class,'updateSetting']);

//    team_permission
    Route::get('/team_permission',[TeamPermissionController::class,'index']);

    Route::apiResource('resources', 'App\Http\Controllers\Admin\ResourceController');
    
    Route::get('/polices',[PolicyController::class,'polices']);
    Route::get('/faqs',[FaqController::class,'faqs']);


});

//  Application user Apis

Route::group(['prefix' => 'app'], function () {
    require_once base_path('routes/app.php');
//    Route::get('/test-api',[\App\Http\Controllers\AppUser\TestController::class,'testApi']);

});
