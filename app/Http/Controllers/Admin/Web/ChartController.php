<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index(Request $request){
        $from = now()->subMonth(1);
        $to = now();
        if($request->filled('month')){
            $from = date("Y-$request->month-01");
            $to = date("Y-$request->month-t");
        }        
        $status = $request->filled('status') ? $request->status : null;
        return view('admin.pages.charts',[
            'appointments'=>$this->getAppointments($status,$from,$to),
            'users'=>$this->getUsers(),
        ]);
    }
    
    private function getAppointments($status = null,$from=null,$to=null){
        
        $appointments = Appointment::
        when(!is_null($status),function($query) use ($status) {
            $query->where('status',$status);
        })
        ->when(isset($from,$to),function($query) use($from,$to) {
            $query->whereBetween('appointment_date',[$from,$to]);
        })
        ->get()->sortBy('appointment_date');
        $data = $this->prepareData($appointments);
        array_push($data,0);
        return [
            'type'=> is_null($status) ? 'Appointments':$status ,
            'labels'=>array_keys($data),
            'data'=>array_values($data)
        ];
    }

    private function prepareData($appointments){
        $data = [];
        foreach($appointments as $appo){
            $date = $appo->appointment_date->format('m-d');
            if(array_key_exists($date,$data)){
                $data[$date] +=1;
            }else{
                $data[$date] = 1;
            }
        }
        return $data;
    }

    private function getDays(){
        $days = [];
        $month = date('m');
        $year = date("Y");
        $dcount = cal_days_in_month(CAL_GREGORIAN, $month, $year); 

        for($d=1; $d<=$dcount; $d++)
        {
            $time=mktime(12, 0, 0, $month, $d, $year);          
            if (date('m', $time)==$month)       
                $days[]=date('m/d', $time);
        }
        return $days;
    }

    private function getUsers(){
        $users = User::get()->sortBy('created_at');
        $data = [];
        foreach($users as $user){
            $date = $user->created_at->format('m-d');
            if(array_key_exists($date,$data)){
                $data[$date] +=1;
            }else{
                $data[$date] = 1;
            }
        }
        return ['labels'=>array_keys($data),'data'=>array_values($data)];
    }
    
}