<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentOrderResource;
use App\Models\Appointment;
use App\Models\AppointmentServices;
use App\Models\Center;
use App\Models\Gift;
use App\Models\MemberShip;
use App\Models\Notification;
use App\Models\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use App\Repositories\AppointmentRepository;
use App\Http\Controllers\ApiController;
use App\Http\Resources\appointmentResource;
use App\Models\Admin;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Models\MemberDay;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
class AppointmentController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    private $appointmentRepository;
    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;

    }
    public function appointmentCalendar()
    {

        $appointment=Appointment::with('user')->get();
    //   return  $this->respondSuccess(["appointment"=>$appointment]);
        return appointmentResource::collection($appointment);
    }
    public function calDate()  {
        return $this->respondError(__('api.sorry'));
        $date1=carbon::now();
        $date2=$date1->addHour();

        return $this->respondSuccess([
            'now'=>$date1,
            'after'=>$date2->addHour()
        ]);
    }

    
    public function  allAppointments(Request $request)
    {

        $limit = $request->get('limit') ?: 10;
        if ($limit > 30) $limit = 30;
        $categories = $this->appointmentRepository->getAppointments($request)->paginate($limit);
        return $this->respondSuccess($categories->all(), $this->createApiPaginator($categories));

    }
    public function getCenterCategories($id) {
        $user=Admin::query()->with('center')->find( auth('sanctum')->id());
        $center=$user->center;
        $categories=CenterCategory::where('center_id',$center->id)->get();
        return $this->respondSuccess([
            'categories'=>$categories,
            'center' =>$center
        ]);
    }
    public function getCenterCategoriesForAdmin($id) {
        $categories=CenterCategory::where('center_id',$id)->get();
        return $this->respondSuccess([
            'categories'=>$categories
        ]);
    }

    public function getCenterCategoriesForCenter(Request $request) {
        $id=$request->get('center_id');
        $categories=CenterCategory::where('center_id',$id)->get();
        return $this->respondSuccess([
            'categories'=>$categories
        ]);
    }
    public function allServices(Request $request) {
        $id=$request->get('center_id');
        $services=CenterService::query()->get();
        return $this->respondSuccess([
            'services'=>$services
        ]);
    }
    public function newAppointment(Request $request )
    {
        $request->validate([
            'services' => ['required','array'],
            'services.*' => ['required','exists:center_services,id'],
            'member' => ['required',],
            'shift' => ['required','date_format:H:i'],
            'appointment_date' => ['required','date_format:Y-m-d'],
        ]);
        if($request->appointment_date < now()->format('Y-m-d')){
            return $this->respondError(['message'=>'Date you chose is invalid']);
        }
        if($request->filled('membership_id') && $request->filled('gift_id')){
            return $this->respondError(['message'=>'You can only chose either membership or gift']);
        }
        if($request->filled('services')){
            $services=CenterService::whereIn('id',$request->services)->get();
        }
        if($request->filled('membership_id'))
        {
            $membership=auth()->user()->memberships()->wherePivot('membership_id',$request->membership_id)->first();
            if(is_null($membership)){
                return $this->respondError(['message'=>'No Membership found']);
            }
            if($membership->pivot->remaining == 0 || $membership->pivot->status != 'valid' || $membership->end_date <= now()){
                return $this->respondError(['message'=>'Your MemberShip is not valid or You exceeded your Sessions']);
            }
            $services=$membership->services;
        }
        if(count($services)<1){
            return $this->respondError(['message'=>'No Services Available']);
        }
        
        $tax = Setting::first()->tax ?? 2;
        $total=0;
        $servicesTimes=0;
        $centerId=0;
        foreach($services as $service)
        {
            $total+=$service->new_price ? $service->new_price : $service->retail_price;
            $servicesTimes += $service->Duration;
            $centerId=$service->center_id;
        }
        
        if(!isset($centerId) || empty($centerId)){
            return $this->respondError(['message'=>'something went wrong']);
        }
        $totalServicesTime=$servicesTimes;
        $total = ceil($total + ($tax/100*$total));
        $endTime = Carbon::parse($request->get('shift'))->addMinutes($servicesTimes);
        
        $usingBy='online';
        $usingBy_id='';
        //using gift
        if($request->filled('gift_id'))
        {
            $gift=Gift::find($request->gift_id);
            if($gift->user_id != auth('client')->user()->id){
                return $this->respondError(['message'=>'No gift found']);
            }
            if($total > $gift->value || $gift->status != 'valid'){
                return $this->respondError(['message'=>'sorry The value of the card is insufficient']);
            } else {
                $gift->remaining = $gift->remaining - ceil($total+(($tax*0.01)*$total));
                $gift->save();
                $usingBy='gift_card';
                $usingBy_id = $request->get('gift_id');
                $payload = [
                    'gift before'=>$gift->value,
                    'gift after'=>(int)$gift->remaining,
                ];
            }
        }
        //using_memberShip
        if($request->filled('membership_id'))
        {
            $usingBy='membership';
            $usingBy_id = $request->get('membership_id');
            $membership->pivot->remaining -= 1 ;
            if($membership->pivot->remaining == 0){
                $membership->pivot->status = 'suspended';
            }
            $membership->push();
            $payload = [
                'session before'=>$membership->session,
                'session after'=>$membership->pivot->remaining,
            ];
        }

        $newAppointment=new Appointment();
        $newAppointment->user_id=auth('client')->id();
        $newAppointment->center_id=$centerId;
        $newAppointment->member_id=$request->get('member')??-1;
        $newAppointment->status='booked';
        $newAppointment->total =$total;
        $newAppointment->total_time =$totalServicesTime;
        $newAppointment->shift_start =$request->get('shift');
        $newAppointment->using_by =$usingBy;
        $newAppointment->usingBy_id = $usingBy_id;
        $newAppointment->shift_end =$endTime->format('H:i');
        $newAppointment->appointment_date =$request->get('appointment_date');
        $newAppointment->save();

        foreach($services as $service)
        {
            $serviceAppointment = new AppointmentServices();
            $serviceAppointment->appointment_id =  $newAppointment->id;
            $serviceAppointment->title = $service->name;
            $serviceAppointment->price =$service->new_price?$service->new_price:$service->retail_price;
            $serviceAppointment->center_services_id = $service->id;
            $serviceAppointment->save();
        }
        
        //Notification
        $admin = Admin::where([['center_id',$centerId],['center_position','owner']])->first();
        $centerSetting = DB::table('center_settings')->where([['center_id',$newAppointment->center_id],['key','create_appointments']]);
        
        if($admin && !is_null($admin->firebase_token)){
            $noti = Notification::create([
                'notifiable_id'=>$admin->id,
                'notifiable_type'=>get_class($admin),
                'payload'=>[
                    'id'=>$newAppointment->id,
                    'type'=>class_basename($newAppointment),
                    'title'=>'Booked',
                    'message'=>"Appointment has been Booked"
                ],
            ]);
            if($centerSetting->exists() && $centerSetting->first()->value == 1){
                $res = $noti->sendToTopic('Booking','New appointment is booked',$admin->firebase_token,'Appointment',$newAppointment->id);
            }
        }
        
        return $this->respondSuccess(['message'=>'Appointment created successfuly','payload'=>$payload ?? '']);
    }

    public function avaliableShifts(Request $request) {
        //get Center
        $date=$request->get("appointment_date");
        $member=$request->get('member');
       $centerId=$request->get('center_id');
       $MemberDays=MemberDay::where('admin_id',$member)->get();
        $duration =15;
       $start ='';
       $end ='';

        foreach($MemberDays as $day){
            if(Carbon::parse($date)->dayName == $day->day)
            {
                $start=$day->start;
                $end=$day->end;
            }
        }
        //separate  time
        $arra=[];
        $begin = new DateTime($start);
        $end = new DateTime($end);
        $interval = DateInterval::createFromDateString('15 minute');
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $arra[]= $dt->format("H:i");
        }

        return $this->respondSuccess( $arra);

    }

    public function checkDate(Request $request){
        $date=$request->get("appointment_date");
        $centerId=$request->get('center_id');
        $center= Center::with('days')->find($centerId);

        $day= Carbon::parse($date)->locale('en')->dayName; 
        $start="";
        $finish="";
        $workDays = $center->days;
        foreach ($workDays as $workDay)
        {
            if($day == $workDay->day){
                $start = $workDay->open->format('H:i');
                $finish = $workDay->close->format('H:i');
            }
        }

        if ($start == "")
        {
            return  $this->respondError("sorry center close ".$day);
        }
        //separate  time
        $arra=[];
        $begin = new DateTime($start);
        $end = new DateTime($finish);
        $interval = DateInterval::createFromDateString('15 minute');
        $period = new DatePeriod($begin, $interval, $end);
        foreach ($period as $dt) {
            $arra[]= $dt->format("H:i");
        }

        return $this->respondSuccess([
            'open'=>$start,
            'close'=>$finish,
            'day'  =>$day,
            'times'=>$arra,
        ]);

    }

    public function selectMember(Request $request){
       $time = $request->get('time');
       //convert to carbon time
       $selctedTime=   Carbon::createFromFormat('H:i', $time);

        $date=$request->get('appointment_date');
        $centerId=$request->get('center_id');
        
        $members =Admin::where([['center_id',$centerId],['center_position','member']])->get();
        foreach($members as $key=>$member){
            $member->is_available = true;
            $appointment_exists = Appointment::where([
                ['member_id',$member->id],
                ['appointment_date', $date],
                ['status','booked']
                ])->get();
            if(isset($appointment_exists) && !is_null($appointment_exists)){
                foreach($appointment_exists as $appointment){
                    $startTime= Carbon::createFromFormat('H:i', $appointment->shift_start);
                    $endTime  = Carbon::createFromFormat('H:i', $appointment->shift_end);
                    if($selctedTime->gte($startTime) && $selctedTime->lte($endTime) ){
                        // $member->is_available = false;
                        $members->forget($key);
                    }
                }
            }   
        }
        return $this->respondSuccess(['team'=>$members->values()]);
    }

    public function reviewBeforeConfirm(Request $request){
        $request->validate(
            [
                'services' => 'required',
                'member_id' => 'required',
                'time' => 'required',
                'appointment_date' => 'required',
                'center_id'        =>'required'

            ],
            [
                'services.required' => 'please chose at least 1 service', // custom message
                'member.required' => 'member is Required', // custom message
                'shift.required' => 'shift is Required', // custom message
                'appointment_date.required' => 'appointment date is Required', // custom message
            ]
        );
        $total=0;
        $servicesTimes=[];
        $services=CenterService::whereIn('id',$request->get('services'))->get();
        foreach($services as $service)
        {
            $total+=(integer)$service->retail_price;
            $servicesTimes[]=$service->Duration;
        }
        $totalServicesTime=self::getTotalDuration($servicesTimes,'total');

        $valueShift=self::getTotalDuration($servicesTimes,'parts');

        $center = Center::with('images')->find($request->get('center_id'));
        return $this->respondSuccess([
            'totalTime'=>$totalServicesTime,
            'totalPrice'=>$total,
            'center' =>$center,
        ]);
    }

    public function memberInfo(Request $request){
        $member_id=$request->get('member_id');
        $member =Admin::find($member_id);
        if(!$member)
        {
            return $this->respondError("sorry not found member");
        }
        return $this->respondSuccess([
            'member'=>$member
        ]);
    }
    
    public function allGigts(Request $request){
        $gifts = Gift::query()
            ->when(!$request->filled('center_id'),function($query){
                $query->where('user_id', auth('client')->user()->id);
            })
            ->when($request->filled('center_id'),function($query)use($request){
                $query->where('center_id', $request->center_id);
            })
            ->orderBy('created_at', 'desc')->get();
        return $this->respondSuccess(['gifts'=> $gifts]);
    }

    public function allMembership(Request $request ){
        $memberships = auth('client')->user()->memberships()->with('services')->get();
        if($request->filled('center_id')){
            $memberships = MemberShip::with(['services:id,name'])
                ->where('center_id', $request->center_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        return $this->respondSuccess(['membership'=> $memberships]);
    }

    public function getAppointment(Request $request){
        $user =User::find(auth('client')->id());
        $appointments=Appointment::whereUserId($user->id)->with('center')->with('appointmentServices')->orderBy('created_at', 'desc')->get();
        return AppointmentOrderResource::collection($appointments);
    }
    
    public function getAppointmentId(Request $request,$id){
        $user =User::find(auth('client')->id());
        $appointments=Appointment::whereId($id)->with('center')->with('appointmentServices')->get();
        if(!is_null($appointments)){
           return AppointmentOrderResource::collection($appointments); 
        }else{
            return $this->respondError(['message'=>'appointment is not found']);
        }
        
    }
    
    public function editAppointment(Request $request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
                'appointment_id'=>['required','exists:appointments,id'],
                'appointment_date'=>['required','date_format:Y-m-d'],
                'shift'=>['required','date_format:H:i'],
                'edit_type'=>['required','in:rebook,reschedule']
            ]);
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'payload'=>$validator->errors()]);
        }
        if($request->appointment_date < now()->format('Y-m-d')){
            return $this->respondError(['message'=>'Error','payload'=>'Date you choose is invalid']);
        }
        if($this->appointmentDateNotAvailable($request)){
            return $this->respondError(['message'=>'Error','payload'=>'You need to choose another time']);
        }
        $appointment = Appointment::find($request->appointment_id);
        if(in_array($appointment->status,['completed','canceled'])){
            return $this->respondError(['message'=>'Error','payload'=>'Appointment can\'t be edited']);
        }
        $appointment->appointment_date = $request->appointment_date;
        $appointment->shift_start = $request->shift;
        $appointment->shift_end = Carbon::parse($request->shift)->addMinutes($appointment->total_time)->format('H:i');
        $appointment->save();
        $not = new Notification();
        $admin = Admin::where([['center_id',$appointment->center_id],['center_position','owner']])->first();
        $noti = Notification::create([
                'notifiable_id'=>$admin->id,
                'notifiable_type'=>get_class($admin),
                'payload'=>[
                    'id'=>$appointment->id,
                    'type'=>class_basename($appointment),
                    'title'=>ucfirst($request->edit_type),
                    'message'=>"Appointment has been ".ucfirst($request->edit_type)
                ],
            ]);
        $centerSetting = DB::table('center_settings')->
        where([['center_id',$appointment->center_id],['key','reschedule_appointments']]);
        if($admin && !is_null($admin->firebase_token)){
            if($centerSetting->exists() && $centerSetting->first()->value == 1){
                $status = ucfirst($request->edit_type);
                $noti = (new Notification())->sendToTopic($status,"$status Appointment",$admin->firebase_token,'Appointment',$appointment->id);
            }
        }
        return $this->respondSuccess(['message'=>'Success','payload'=>'Appointment is updated']);
    }
    
    public function appointmentDateNotAvailable($request){
        $appointment = Appointment::with('appointmentServices:id,appointment_id,center_services_id')->find($request->appointment_id);
        $checkedAppointment = Appointment::with('appointmentServices:id,appointment_id,center_services_id')
        ->where([
            ['center_id',$request->center_id],
            ['appointment_date',$request->appointment_date],
            ['shift_start',$request->shift],
            ['status','booked'],
            ])
        ->when($appointment->member_id != -1,function($query)use($appointment){
            $query->where('member_id',$appointment->member_id);
        })->first();
        if(is_null($checkedAppointment)){
            return false;
        }
        $status = false;
        foreach($appointment->appointmentServices as $service){
            if($checkedAppointment->appointmentServices->contains('center_services_id','=',$service->center_services_id)){
                $status = true;
            }
        }
        return $status;
    }
    
    public function cancelReason(Request $request){
        $request->validate([
            'center_id' =>['required','exists:centers,id'],
        ]);
        $reasons = DB::table('center_cancelation_reasons')->where('center_id',$request->center_id)->get();
        return $this->respondSuccess($reasons);
    }

    public function cancelAppointment(Request $request)
    {
        $id = $request->get('appointment_id');
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return $this->respondError('appointment not found');
        }
        if($appointment->status != 'booked'){
            return $this->respondError('appointment can\'t be canceled'); 
        }
        $date = explode("-",$appointment->appointment_date);
        $time = explode(":",$appointment->shift_start);
        $day = explode(" ",$date[2])[0];
        $tz = "+3";
        $fulldate = Carbon::create($date[0],$date[1],$day,$time[0],$time[1],0,$tz);
        $cancelation_range = (int) DB::table('center_settings')
        ->where([
            ['center_id',$request->center_id],
            ['key','cancelation_range']
        ])->first()?->value ?? 6;
        if(date_diff($fulldate,now($tz))->format("%H") < $cancelation_range)
        {
            return $this->respondError('You cant cancel the appointment');
        }
        $appointment->status = 'canceled';
        $appointment->note = 'Canceled - '.$request->cancelation_reason ?? '';
        $appointment->save();
        $usingBy = match($appointment->using_by){
            'online'=>'online',
            'gift_card'=>Gift::find($appointment->usingBy_id),
            'membership'=>MemberShip::find($appointment->usingBy_id),
        };
        switch($appointment->using_by){
            case 'online':                
                break;
            case 'gift_card':
                $this->restoreGift($usingBy,$appointment);
                break;
            case 'membership':
                $this->restoreMembership($usingBy);
                break;
            default;            
        }
        $admin = Admin::where([['center_id',$appointment->center_id],['center_position','owner']])->first();
        $centerSetting = DB::table('center_settings')->where([['center_id',$appointment->center_id],['key','cancel_appointments']]);
        
        if($admin && !is_null($admin->firebase_token)){
            $noti = Notification::create([
                'notifiable_id'=>$admin->id,
                'notifiable_type'=>get_class($admin),
                'payload'=>[
                    'id'=>$appointment->id,
                    'type'=>class_basename($appointment),
                    'title'=>'Cancelation',
                    'message'=>"Appointment has been Canceled"
                ],
            ]);
            if($centerSetting->exists() && $centerSetting->first()->value == 1){
                $noti = (new Notification())->sendToTopic('Cancelation','Appointment has been canceled',$admin->firebase_token,'Appointment',$appointment->id);
            }
        }
        return $this->respondSuccess();
    }

    private function restoreGift($gift,$appointment)
    {
        $gift->remaining += $appointment->total + (0.02*$appointment->total);
        return $gift->save();
    }

    private function restoreMembership($membership)
    {
        $membership->remaining +=1;
        return $membership->save();
    }
}
