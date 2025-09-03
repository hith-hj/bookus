<?php

namespace App\Http\Controllers\Center;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Center\Repos\CenterAppointmentsRepository as AppoRepo;
use App\Models\Category;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Center;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Models\Contact;
use App\Models\OpenDay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\carbon;
use DB;

class CenterAppointmentsController extends ApiController
{
    public $appoRepo;
    public function __construct(AppoRepo $appoRepo){
        $this->appoRepo = $appoRepo;
    }
    
    public function getCenterAppointments(Request $request,$pageLimit = 20)
    {
        $center_id = $request->center_id ?? auth()->user()->center_id;
        $appointments = Appointment::with(['appointmentServices','user:id,first_name,last_name,email,image,code','member:id,first_name,last_name'])
        ->where('center_id', $center_id)
        ->when($request->filled('appointment_id'),function($query) use ($request){
            $query->where('id',$request->appointment_id);
        })
        ->get();
        return count($appointments)>0?
            $this->respondSuccess(['message'=>'appointment list','appointments'=>$appointments]):
            $this->respondError(['message'=>'no appointments Found']);
    }
    
    public function createCenterAppointment(Request $request)
    {
        $validator = $this->appoRepo->validateAppointmentDetails($request->all());
        if($validator->fails()){
            return $this->resBack(['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()]);
        }
        
        return $this->resBack($this->appoRepo->addCenterAppointment($request),'Appointment');
    }
    
    public function editCenterAppointment(Request $request)
    {
        return $this->resBack($this->appoRepo->editCenterAppointment($request),'Appointment');
    }
    
    public function cancelCenterAppointment(Request $request)
    {
        return $this->resBack($this->appoRepo->cancelCenterAppointment($request),'Appointment');
    }
    
    public function completeCenterAppointment(Request $request)
    {
        return $this->resBack($this->appoRepo->completeCenterAppointment($request),'Appointment');
    }
    
}