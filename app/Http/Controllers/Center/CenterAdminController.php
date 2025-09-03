<?php

namespace App\Http\Controllers\Center;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Center\Repos\CenterAdminRepository as CenterRepo;
use App\Models\Category;
use App\Models\Service;
use App\Models\Center;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Models\Contact;
use App\Models\OpenDay;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\carbon;


class CenterAdminController extends ApiController
{
    private $centerRepo;
    private const Deleted = -1;

    public function __construct(CenterRepo $centerRepo)
    {
        $this->centerRepo = $centerRepo;
    }

    //** Start Center */

    public function centerDetails($center_id)
    {
        $center = Center::find($center_id);
        if(is_null($center) ){
           return $this->respondError(['message'=>'No Center Found','status'=>2]);
        }
        Offer::checkOffersStatus($center_id);
        return match($center->status){
            -1 => $this->respondError(['message'=>'center is Deactivated','status'=>-1,]),
            0 =>$this->respondError(['message'=>'center not Verified','status'=>0,]),
            1 =>$this->respondSuccess(['message'=>'center Found','status'=>1,'Center'=>$center]),
        };
                    
    }

    public function createCenter(Request $request)
    {
        $checkCenterExist = $this->centerRepo->checkCenterExists($request->name,$request->email);
        if($checkCenterExist){
            return $checkCenterExist->status == self::Deleted ? 
                $this->respondError(['message'=>'Center is Deleted']) : 
                $this->respondError(['message'=>'Center does Exists']); 
        }

        $validator = $this->centerRepo->validateCenterDetails($request->all());
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'Errors'=>$validator->errors()]);
        }
        $stat = $this->centerRepo->addCenterDetails($request);
        
        return $stat['stat'] == true ?
            $this->respondSuccess(['message'=>'center information stored','Center'=>$stat['center']]) : 
            $this->respondError(['message'=>'something went wrong','Errors'=>$stat]);
    }

    public function editCenter(Center $center,Request $request)
    {
        $admin = auth()->user();        
        if($admin->center_position != 'owner' || $admin->center_id  != $center->id || !$admin->hasRole('Center')){
            return $this->respondError(['message'=>'You Can not Edit Centers']);
        }

        if($center->status == self::Deleted){
            return $this->respondError(['message'=>'Center is Deleted']) ;
        }

        $validator = $this->centerRepo->validateCenterDetailsUpdate($request->all());
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'Errors'=>$validator->errors()]);
        }
        if($center->email != $request->email){
            $validator = Validator::make($request->all(),[
                    'email'=>['unique:centers,email'],
                ]);
            if($validator->fails()){
                return $this->respondError(['message'=>$validator->errors()->first(),'Errors'=>$validator->errors()]);
            }
        }
        $stat = $this->centerRepo->centerUpdateDetails($request,$center);
        
        return $stat['stat'] == true ? 
            $this->respondSuccess(['message'=>'center information updated','Center'=>$stat['center']]) : 
            $this->respondError(['message'=>'something went wrong','Errors'=>$stat]);
    }

    public function deleteCenter(Center $center)
    {
        $admin = auth()->user();
        if($admin->center_position != 'owner' || $admin->center_id  != $center->id || !$admin->hasRole('Center') || $center->status != 1){
            return $this->respondError(['message'=>'You Can not Delete Centers']);
        }
        $center->status = -1;
        $center->save();
        return $this->respondSuccess(['message'=>'center has been deleted']);
    }
    
    public function requestCenterVerification(Request $request)
    {
        return $this->requestCenterActivation($request);
    }
    
    public function requestCenterActivation(Request $request){
        $center = Center::find($request->center_id);
        if(!$center){
            return $this->respondError(['message'=>'Error,Center id is required',]);
        }
        if($center->status != 0){
            return $this->respondError(['message'=>'Error,Center can\'t be activated']);
        }
        $center->update(['status'=>1]);
        return $this->respondSuccess(['message'=>'Center Verified']);
    }
    
    // public function requestCenterActivation(Request $request)
    // {
    //     $center = Center::find($request->center_id);
    //     if(!$center){
    //         return $this->respondError(['message'=>'Error,Center id is required',]);
    //     }
    //     if($center->status == 1){
    //         return $this->respondError(['message'=>'Error,You cant request activation for an active account',]);
    //     }
    //     $adminRequest = DB::table('admin_requests')
    //          ->where([
    //              ['requester_id',auth('admin')->user()->id,],
    //              ['requested_id',$request->center_id,]
    //          ])->first();
    //     if($adminRequest){
    //         if(Carbon::parse($adminRequest->created_at)->addDay(1) > now()){
    //             return $this->respondSuccess(['message'=>'you can not send another request yet']);
    //         }else{
    //             $center->update(['status'=>1]);
    //             return $this->respondSuccess(['message'=>'Center Verified']);
    //         }
    //     }
    //     $request['requester_type'] = 'admin';
    //     $request['requested_type'] = 'center';
    //     $type = $center->status == 0 ? 'verification': 'activation';
    //     $activateRequest = DB::table('admin_requests')->insert([
    //     'requester_id'=>auth()->user()->id,
    //     'requester_type'=>$request->requester_type,
    //     'requested_id'=>$request->center_id,
    //     'requested_type'=>$request->requested_type,
    //     'type'=> $type,
    //     'payload'=> "$type Request",
    //     ]);
    //     return $this->respondSuccess(['message'=>$type.' Request Sent','Request'=>"$type Request"]);
    // }
    

    //** End Center  */

    //** Start Days  */

    public function getCenterDays(Request $request)
    {
        $days = OpenDay::where('center_id',$request->center_id)->get();
        return count($days)<=0 ? 
            $this->respondError(['message'=>'no days found']) :
            $this->respondSuccess(['message'=>'days Found','days'=>$days]);        
    }

    public function createCenterDays(Request $request)
    {
        $validator = $this->centerRepo->validateCenterDays($request->all());
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'Errors'=>$validator->errors()]);
        }
        $stat = $this->centerRepo->addCenterDays($request);
        
        return $stat['stat'] == true ? 
            $this->respondSuccess(['message'=>'center days stored','Center-days'=>$stat['payload']]) : 
            $this->respondError(['message'=>'something went wrong','Errors'=>$stat['payload']]);
    }

    public function deleteCenterDay(Request $request)
    {
        $day = OpenDay::findOrFail($request->day_id);
        // if(!$day || $day->center_id != $center_id || auth()->user()->center_id != $center_id){
        //     return $this->respondError(['message'=>'Unauthorized, Day not found']);
        // }
        if($day->center_id != $request->center_id){
            return $this->respondError(['message'=>'Unauthorized , day Cant be Deleted']);
        }
        $day->delete();
        return $this->respondSuccess(['message'=>'Day Deleted']);
    }

    //** end Days  */

    //** start Contacts  */
    
    public function centerAvailableContacts(Request $request)
    {
        $contacts = $this->centerRepo->centerAvailableContacts($request);
        return $this->respondSuccess(['message'=>'Available Contacts','contacts'=>$contacts]); 
    }

    public function getCenterContacts($center_id)
    {
        $contacts = Contact::where('center_id',$center_id)->get();
        return count($contacts )<=0 ? 
            $this->respondError(['message'=>'no contacts found']) :
            $this->respondSuccess(['message'=>'contacts Found','contacts'=>$contacts]);        
    }

    public function createCenterContact(Request $request)
    {
        $validator = $this->centerRepo->validateCenterContact($request->all());
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'Errors'=>$validator->errors()]);
        }
        $stat = $this->centerRepo->addCenterContact($request);
        
        return $stat['stat'] == true ? 
            $this->respondSuccess(['message'=>'center contact stored','Center-contact'=>$stat['payload']]) : 
            $this->respondError(['message'=>'something went wrong','Errors'=>$stat['payload']]);
    }

    public function deleteCenterContact(Request $request)
    {
        $contact = Contact::findOrFail($request->contact_id);
        // if(!$contact || $contact->center_id != $center_id || auth()->user()->center_id != $center_id){
        //     return $this->respondError(['message'=>'Unauthorized,Day not found']);
        // }
        if($contact->center_id != $request->center_id){
            return $this->respondError(['message'=>'Unauthorized , contact Cant be Deleted']);
        }
        $contact->delete();
        return $this->respondSuccess(['message'=>'Contact Deleted']);
    }

    //** end Contacts   */


    //** Start Category  */
    
    public function getCategories()
    {
        $categories = Category::all();
        return count($categories) <=0 ? 
            $this->respondError(['message'=>'No Categories Found']) :
            $this->respondSuccess(['message'=>'Categories Found','categories'=>$categories]) ;
    }

    public function getCenterCategories($center_id)
    {
        $categories = CenterCategory::where('center_id',$center_id)->get(['id','name','center_id']);
        return count($categories) <=0 ? 
            $this->respondError(['message'=>'No Categories Found']) :
            $this->respondSuccess(['message'=>'Categories Found','categories'=>$categories]) ;
    }

    public function createCenterCategories(Request $request)
    {
        $validator = $this->centerRepo->validateCenterCategories($request->all());
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'Errors'=>$validator->errors()]);
        }

        $stat = $this->centerRepo->addCenterCategories($request);

        return $stat['stat'] == true ? 
            $this->respondSuccess(['message'=>'center categories stored','Center-categories'=>$stat['payload']]) : 
            $this->respondError(['message'=>'something went wrong','Errors'=>$stat['payload']]);
    }

    public function deleteCenterCategories(Request $request)
    {
        $category = CenterCategory::findOrFail($request->category_id);
        $services = CenterService::where('center_category_id',$request->category_id)->get(['id']);
        if($category->center_id != $request->center_id){
            return $this->respondError(['message'=>'unauthorized,Category is not deleted']);
        }
        if(count($services)>=0){
            CenterService::destroy($services);   
        }
        $category->delete();
        return $this->respondSuccess(['message'=>'Category Deleted']);
    }

    //** End Category  */
    
    //** start Services */
    
    public function getServices()
    {
        $services = Service::all(['id','name']);
        return count($services)<=0 ? 
            $this->respondError(['message'=>'No Services Found']) :
            $this->respondSuccess(['message'=>'Services Found','Services'=>$services]);
    }
    
    public function getCenterServices($center_id)
    {
        $centerServices = CenterService::where('center_id',$center_id)->get();
        return count($centerServices)<=0 ? 
            $this->respondError(['message'=>'No Services Found']) :
            $this->respondSuccess(['message'=>'Services Found','Services'=>$centerServices]);
    }

    public function createCenterServices(Request $request)
    {
        $validator = $this->centerRepo->validateCenterServices($request->all());
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'Errors'=>$validator->errors()]);
        }
        $stat = $this->centerRepo->addCenterServices($request);

        return $stat['stat'] == true ? 
            $this->respondSuccess(['message'=>'Center Services stored','Center-Services'=>$stat['payload']]) : 
            $this->respondError(['message'=>'something went wrong','Errors'=>$stat['payload']]);
    }  
    
    public function deleteCenterServices(Request $request)
    {
        $service = CenterService::findOrFail($request->service_id);
        if($service->center_id != $request->center_id){
            return $this->respondError(['message'=>'unauthorized,Service is not deleted']);
        }
        $service->delete();
        return $this->respondSuccess(['message'=>'Service Deleted']);
    }
    
    //** End Services */
    
    //** start Members */
    
    public function getCenterMembers(Request $request)
    {
        $stat = $this->centerRepo->getCenterMembers($request);
        return $this->resBack($stat,'Members');
    }
    
    public function createCenterMember(Request $request)
    {
        $validator = $this->centerRepo->validateMemberDetails($request->all());
        if($validator->fails()){
            return $this->resBack(['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()]);
        }
        // dd('passed');
        $stat = $this->centerRepo->addCenterMember($request);
        return $this->resBack($stat,'Member');
    }
    
    public function deleteCenterMember(Request $request)
    {
        $stat = $this->centerRepo->deleteCenterMember($request);
        return $this->resBack($stat,'Member');
    }
    
    //** End Members */
    
    //** start Offers */
    
    public function getCenterOffers(Request $request)
    {
        $stat = $this->centerRepo->getCenterOffers($request);
        return $this->resBack($stat,'Offers');
    }
    
    public function createCenterOffer(Request $request)
    {
        $stat = $this->centerRepo->addCenterOffer($request);
        return $this->resBack($stat,'Offer');
    }
    
    public function deleteCenterOffer(Request $request)
    {
        $stat = $this->centerRepo->deleteCenterOffer($request);
        return $this->resBack($stat,'Offer');
    }
    
    public function deactivateCenterOffer(Request $request)
    {
        $stat = $this->centerRepo->deactivateCenterOffer($request);
        return $this->resBack($stat,'Offer');
    }
    
    //** End Offers */
    
    //** start Branch*/
    
    public function getCenterBranches(Request $request)
    {
        $stat = $this->centerRepo->getCenterBranches($request);
        return $this->resBack($stat,'Branches');
    }
    
    
    public function createCenterBranch(Request $request)
    {
        $stat = $this->centerRepo->addCenterBranch($request);
        return $this->resBack($stat,'Branch');
    }
    
    public function editCenterBranch(Request $request)
    {
        $stat = $this->centerRepo->editCenterBranch($request);
        return $this->resBack($stat,'Branch');
    }
    
    public function deleteCenterBranch(Request $request)
    {
        $stat = $this->centerRepo->deleteCenterBranch($request);
        return $this->resBack($stat,'Branch');
    }
    
    //** end Branch*/
    
    //** start holiday */
    
    public function getCenterHolidays(Request $request)
    {
        return $this->resBack($this->centerRepo->getCenterHolidays($request),'Holidays');
    }
    
    public function createCenterHoliday(Request $request)
    {
        return $this->resBack($this->centerRepo->createCenterHoliday($request),'Holidays');
    }
    
    public function deleteCenterHoliday(Request $request)
    {
        return $this->resBack($this->centerRepo->deleteCenterHoliday($request),'Holidays');
    }
     
    public function getMembersHolidays(Request $request)
    {
        return $this->resBack($this->centerRepo->getMembersHolidays($request),'Members Holidays');   
    }
    
    public function createMemberHoliday(Request $request)
    {
        return $this->resBack($this->centerRepo->createMemberHoliday($request),'Member Holiday');   
    }
    
    public function deleteMemberHoliday(Request $request)
    {
        return $this->resBack($this->centerRepo->deleteMemberHoliday($request),'Member Holiday');   
    }
    
    public function addMemberService(Request $request)
    {
        return $this->resBack($this->centerRepo->addMemberService($request),'Member Service');
    }
    
    public function deleteMemberService(Request $request)
    {
        return $this->resBack($this->centerRepo->deleteMemberService($request),'Member Service');
    }
    
    public function addMemberDay(Request $request)
    {
        return $this->resBack($this->centerRepo->addMemberDay($request),'Member Day');
    }
    
    public function deleteMemberDay(Request $request)
    {
        return $this->resBack($this->centerRepo->deleteMemberDay($request),'Member Day');
    }
    
    //** end holiday */
    
    //** start resources */
        
    public function getCenterResources(Request $request)
    {
        return $this->resBack($this->centerRepo->getCenterResources($request),'Resources');
    }
    
    public function createCenterResource(Request $request)
    {
        return $this->resBack($this->centerRepo->createCenterResource($request),'Resources');
    }
    
    public function deleteCenterResource(Request $request)
    {
        return $this->resBack($this->centerRepo->deleteCenterResource($request),'Resources');
    }
    
    //** end resources */
    
    //** start dayoff */
        
    public function getCenterDaysoff(Request $request)
    {
        return $this->resBack($this->centerRepo->getCenterDaysoff($request),'Daysoff');
    }
    
    public function createCenterDayoff(Request $request)
    {
        return $this->resBack($this->centerRepo->createCenterDayoff($request),'Dayoff');
    }
    
    public function deleteCenterDayoff(Request $request)
    {
        return $this->resBack($this->centerRepo->deleteCenterDayoff($request),'Dayoff');
    }
    
    //** end dayoff */
    
    //** start cancel reasons */
        
    public function getCenterCancelationReasons(Request $request)
    {
        return $this->resBack($this->centerRepo->getCenterCancelationReasons($request),'cancel reason');
    }
    
    public function createCenterCancelationReason(Request $request)
    {
        return $this->resBack($this->centerRepo->createCenterCancelationReason($request),'cancel reason');
    }
    
    public function deleteCenterCancelationReason(Request $request)
    {
        return $this->resBack($this->centerRepo->deleteCenterCancelationReason($request),'cancel reason');
    }
    
    //** end cancel reasons */
    
    //** start reminders */
        
    public function getCenterReminders(Request $request)
    {
        return $this->resBack($this->centerRepo->getCenterReminders($request),'Reminders');
    }
    
    public function createCenterReminder(Request $request)
    {
        return $this->resBack($this->centerRepo->createCenterReminder($request),'Reminders');
    }
    
    public function updateCenterReminder(Request $request)
    {
        return $this->resBack($this->centerRepo->updateCenterReminder($request),'Reminders');
    }
    
    public function toggleCenterReminder(Request $request)
    {
        return $this->resBack($this->centerRepo->toggleCenterReminder($request),'Reminders');
    }
    
    public function deleteCenterReminder(Request $request)
    {
        return $this->resBack($this->centerRepo->deleteCenterReminder($request),'Reminders');
    }
    
    //** end reminders*/

    //** start settings*/
    public function getCenterSettings(Request $request)
    {
        return $this->resBack($this->centerRepo->getCenterSettings($request),'Settings');
    }
    
    public function setCenterSettings(Request $request)
    {
        return $this->resBack($this->centerRepo->setCenterSettings($request),'Settings');
    }
    
    //** end settings*/
    
    //** start Notification*/
    public function getCenterNotifications(Request $request)
    {
        return $this->resBack($this->centerRepo->getCenterNotifications($request),'Notification');
    }
    
    public function setCenterNotificationSeen(Request $request)
    {
        return $this->resBack($this->centerRepo->setCenterNotificationSeen($request),'Notification');
    }
    //** end Notification*/
    
}
