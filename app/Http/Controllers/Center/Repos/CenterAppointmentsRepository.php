<?php

namespace App\Http\Controllers\Center\Repos;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Image;
use App\Models\Admin;
use App\Models\Center;
use App\Models\Appointment;
use App\Models\AppointmentServices;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Models\Contact;
use App\Models\OpenDay;
use App\Models\User;
use App\Models\Setting;
use App\Models\Notification;
use Carbon\Carbon;
use DB;


class  CenterAppointmentsRepository
{
    public function addCenterAppointment($request)
    {
        if($request->appointment_date < now()->format('Y-m-d')){
            return  ['stat'=>false,'message'=>'Error','payload'=>'Appointment can\'t be created the date you entered is invalid'];
        }
        $tax = Setting::first()->tax ?? 2;
        $total=0;
        $servicesTimes= 0;
        $services=CenterService::whereIn('id',$request->get('services'))->get();
        foreach($services as $service)
        {
            $total+=$service->new_price ? $service->new_price : $service->retail_price;
            $servicesTimes += $service->Duration;
        }
        $totalServicesTime=$servicesTimes;
        $total = ceil($total + ($tax/100*$total));
        $startTime=Carbon::parse($request->get('shift'));
        $startTime->addMinutes($servicesTimes);
        
        $newAppointment= Appointment::create([
                'total' => $total,
                'total_time' => $totalServicesTime,
                'shift_end' => $startTime->format('H:i'),
                'using_by' => 'online',
                'usingBy_id' => '',
                'status' => 'booked',
                'note'=>$request->note,
                'user_id' => $request->user_id,
                'center_id' => $request->center_id,
                'member_id' => $request->member_id ?? -1,
                'shift_start' => $request->get('shift'),
                'appointment_date' => $request->get('appointment_date'),
            ]);

        $appointmentServices = [];
        foreach($services as $service)
        {
            $appointmentServices[] = AppointmentServices::updateOrCreate([
                'appointment_id' => $newAppointment->id,
                'center_services_id' => $service->id
                ],
                [
                    'title' => $service->name,
                    'price' => $service->new_price ? $service->new_price : $service->retail_price,
                    'center_services_id' => $service->id,
                ]);
        }
        $user = User::find($request->user_id,['id','first_name','last_name','email','code','firebase_token','active_notification']);
        $admin = $request->member_id != -1 ? Admin::find($request->member_id) : null;
        $newAppointment->user = $user;
        $newAppointment->appointmentServices = $appointmentServices;
        $noti = Notification::create([
                'notifiable_id'=>$user->id,
                'notifiable_type'=>get_class($user),
                'payload'=>[
                    'id'=>$newAppointment->id,
                    'type'=>class_basename($newAppointment),
                    'title'=>'Booked',
                    'message'=>'New Appointment is booked'
                ],
            ]);
            
        if(!is_null($user->firebase_token) && $user->active_notification ){
            $res = $noti->sendToTopic('Appointment booked','New Appointment is booked',$user->firebase_token,'Appointment',$newAppointment->id);
        }    
        if($admin != null && !is_null($admin->firebase_token) && $admin->active_notification){
            $noti->sendToTopic('Appointment booked','New Appointment is booked',$admin->firebase_token,'Appointment',$newAppointment->id);
        }
        return ['stat'=>true,'message'=>'Success','payload'=>$newAppointment];
    }
    
    public function editCenterAppointment($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id' => ['required','exists:centers,id'],
            'appointment_id' => ['required','exists:appointments,id'],
            'appointment_date' => ['required','date_format:Y-m-d'],
            'shift' => ['required','date_format:H:i'],
            'edit_type' => ['required','in:rebook,reschedule'],
        ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        
        if($request->appointment_date < now()->format('Y-m-d')){
            return ['stat'=>false,'message'=>'Error','payload'=>'the date you choose is invalid'];
        }
        
        if($this->appointmentDateNotAvailable($request)){
            return ['stat'=>false,'message'=>'Error','payload'=>'You need to choose another time'];
        }
        
        $appointment = Appointment::find($request->appointment_id);
        
        if($appointment->center_id != $request->center_id){
           return ['stat'=>false,'message'=>'error','payload'=>'Unauthoriz Action'];
        }
        
        if(in_array($appointment->status,['completed','canceled'])){
            return ['stat'=>false,'message'=>'Error','payload'=>'Appointment can not be edited'];
        }
        
        $appointment->appointment_date = Carbon::parse($request->appointment_date);
        $appointment->shift_start = $request->get('shift');
        $appointment->shift_end = Carbon::parse($request->get('shift'))->addMinutes($appointment->total_time)->format('H:i');
        $appointment->save();
        $status = ucfirst($request->edit_type);
        $user = User::find($appointment->user_id);
        $admin = $appointment->member_id != -1 ? Admin::find($appointment->member_id) : null ;
        $noti = Notification::create([
                'notifiable_id'=>$user->id,
                'notifiable_type'=>get_class($user),
                'payload'=>[
                    'id'=>$appointment->id,
                    'type'=>class_basename($appointment),
                    'title'=>$status,
                    'message'=>"Appointment has been $status"
                ],
            ]);
        if($admin != null && !is_null($admin->firebase_token) && $admin->active_notification){
            $noti->sendToTopic("Appointment $status","Appointment $status",$admin->firebase_token,'Appointment',$newAppointment->id);
        }
        if(!is_null($user->firebase_token) && $user->active_notification){
            $noti->sendToTopic(
                "Appointment $status",
                "Your appointment has been $status",
                $user->firebase_token,
                'Appointment',
                $appointment->id
                );
        } 
        return ['stat'=>true,'message'=>'Success','payload'=>"Appointment ".ucfirst($request->edit_type)];
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
    
    public function cancelCenterAppointment($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id' => ['required','exists:centers,id'],
                'appointment_id' => ['required','exists:appointments,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $appointment = Appointment::find($request->appointment_id);
        if($appointment->center_id != $request->center_id){
           return ['stat'=>false,'message'=>'error','payload'=>'Unauthoriz Action'];
        }
        if($appointment->status !== 'booked'){
            return ['stat'=>false,'message'=>'error','payload'=>"Appointment is $appointment->status, can't be canceled"];
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
            return ['stat'=>false,'message'=>'error','payload'=>'You can\'t cancel the appointment'];
        }
        $user = User::find($appointment->user_id);
        $admin = $appointment->member_id != -1 ? Admin::find($appointment->member_id) : null;
        $appointment->status = 'canceled';
        $appointment->save();
        $noti = Notification::create([
                'notifiable_id'=>$user->id,
                'notifiable_type'=>get_class($user),
                'payload'=>[
                    'id'=>$appointment->id,
                    'type'=>class_basename($appointment),
                    'title'=>'Cancelation',
                    'message'=>"Appointment has been Canceled"
                ],
            ]);
        if($user && isset($user->firebase_token) && $user->active_notification == 1){
            $res = $noti->sendToTopic("Appointment Canceled", "Your appointment has been canceled", $user->firebase_token, 'Appointment', $appointment->id);     
        }
        if($admin != null && !is_null($admin->firebase_token) && $admin->active_notification){
            $noti->sendToTopic("Appointment $status","Appointment $status",$admin->firebase_token,'Appointment',$newAppointment->id);
        }
        
        return ['stat'=>true,'message'=>'success','payload'=>'Appointment is canceled'];
    }

    public function completeCenterAppointment($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
                'appointment_id'=>['required','exists:appointments,id'],
            ]);
        if($validator->fails())
        {
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $appointment = Appointment::find($request->appointment_id);
        if($appointment->center_id != $request->center_id)
        {
            return ['stat'=>false,'message'=>'Error','payload'=>'Unauthorized'];
        }
        if($appointment->status != 'booked')
        {
            return ['stat'=>false,'message'=>'Error','payload'=>"Appointment is $appointment->status can't be completed"];
        }
        $appointment->status = 'completed';
        $appointment->save();
        return ['stat'=>true,'message'=>'Success','payload'=>"Appointment completed"];
    }

    public function validateAppointmentDetails($data)
    {
        return Validator::make($data,[
            'user_id'=>['required','string','exists:users,id',],	
            'center_id'=>['required','exists:centers,id',],	
            'member_id'=>['required',],
            'appointment_date'=>['required','date_format:Y-m-d',],
            'shift'=>['required','date_format:H:i',],
            'services'=>['required','array','min:1'],
            'services.*'=>['exists:center_services,id',],
            'note'=>['nullable','string','max:1000'],
            ]);
    }
}