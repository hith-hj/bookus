<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentServices;
use App\Models\Center;
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
use Carbon\Carbon;
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
    public function index()
    {
        //
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
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

    public function getCenterCategoriesForCenter() {
        $id=$this->getCenter();
        $categories=CenterCategory::where('center_id',$id)->get();
        return $this->respondSuccess([
            'categories'=>$categories
        ]);
    }
    public function newAppointment(Request $request )
    {
//         services
// member
// shift
// appointment_date
// client
        $request->validate(
            [
                'services' => 'required',
                'member' => 'required',
                'shift' => 'required',
                'client' => 'required',
                'appointment_date' => 'required',


            ],
            [
                'services.required' => 'please choise at least 1 servace', // custom message
                'member.required' => 'member is Required', // custom message
                'shift.required' => 'shift is Required', // custom message
                'client.required' => 'client is Required', // custom message
                'appointment_date.required' => 'appointment_date is Required', // custom message

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



        $newAppointment=new Appointment();
        $newAppointment->user_id=$request->get('client');
        $newAppointment->center_id=$this->getCenter();
        $newAppointment->member_id=$request->get('member');
        $newAppointment->status='booked';
        $newAppointment->total =$total;
        $newAppointment->total_time =$totalServicesTime;
        $newAppointment->shift_start =$request->get('shift');


        $mins=(integer) $valueShift *15;
        $startTime=Carbon::parse($request->get('shift'));
        $startTime->addMinutes($mins);


        $newAppointment->shift_end =$startTime->format('H:i');
        $newAppointment->appointment_date =$request->get('appointment_date');
    $newAppointment->save();

        foreach($services as $service)
        {

            $serviceAppointment = new AppointmentServices();
            $serviceAppointment->appointment_id =  $newAppointment->id;
            $serviceAppointment->title = $service->name;
            $serviceAppointment->price = $service->retail_price;
            $serviceAppointment->center_services_id = $service->id;
            $serviceAppointment->save();

        }
    return $this->respondSuccess();
        // 'shift_start'
        // 'shift_end'
        // 'appointment_date'

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
            $arra=[];
                $begin = new DateTime($start);
            $end = new DateTime($end);

            $interval = DateInterval::createFromDateString('30 minute');
            $period = new DatePeriod($begin, $interval, $end);

            foreach ($period as $dt) {
                $arra[]= $dt->format("H:i");
            }

//        $center=Center::whereId( $centerId)->first();
//        $appointments =Appointment::
//            where('center_id',$center->id)
//            ->where('member_id',$member)
//            ->where('appointment_date',$date)->get();
//
//
//        if(count( $appointments) > 0){
//            // delete busy shift
//            foreach ($appointments as $key=>$appointment){
//                $length=$appointment->shift_end - $appointment->shift_start;
//                array_splice($fullTimes,$appointment->shift_start,$length);
//            }
//        }


        return $this->respondSuccess( $arra);

    }

    public function getTotalDuration($words,$key)
    {
       $totalHour=0;
       $totalMinute=0;
       $totalPartion=0;

       foreach($words as $word){

        if(str_contains($word, 'h')){
        $posHour=strpos($word, 'h');

            $hour=substr($word,0,$posHour);
            $totalHour+=(integer)$hour;
                    if(str_contains($word, 'm')){
                    $minuteWithUnit=substr($word,$posHour+1);
                    $postionMinute=strpos( $minuteWithUnit,'m');
                    $minute=substr($minuteWithUnit,0,$postionMinute);

                    $totalMinute+=(integer)$minute;
                    }

        }
        else//contain only minute
        {
            $postionMinute=strpos( $word,'m');
            $minute=substr($word,0,$postionMinute);
            $totalMinute+=(integer)$minute;
        }
       }//end foreach
if($totalHour > 0)
$partionH=$totalHour*4;
else
    $partionH=0;

if($totalMinute >= 15){

    $partionM=$totalMinute/15;
    if($totalMinute %15 > 0)
    $partionM=(integer)$partionM + 1;
}

if($totalMinute < 15 && $totalMinute > 0 ){

    $partionM=1;
}

$totalPartion= $partionM+$partionH;
// string total
$stringMunite =0;
$stringHour=0;
$totalString="";
$fromMunitToHour=0;
if($totalMinute > 59)
{
$fromMunitToHour=$totalMinute/60;
            if($totalMinute % 60 > 0){
                $stringMunite =$totalMinute % 60;
            }

}else
{
    $stringMunite =$totalMinute ;
}
if($partionH >0)
$totalString=($partionH +$fromMunitToHour) ."h".$stringMunite ."min";
else
    $totalString=$stringMunite ."min";

if($key=='parts')
return $totalPartion;
if($key=='total'){
return $totalString;

}
        //return cutting index
    }
}
