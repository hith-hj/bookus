<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request){
        $appointments = Appointment::with(['center:id,name','appointmentServices'])
        ->when($request->filled('search'),function($query) use($request){
            return $query->where([['name','like','%'.$request->search.'%']]);
        })->when($request->filled('filter'),function($query) use($request){
            if(in_array($request->filter,['booked','completed','cancelled'])){
                return $query->where('status',$request->filter);
            }                
            if(in_array($request->filter,['online','gift','membership'])){
                return $query->where('using_by','LIKE','%'.$request->filter.'%');
            }                
        })->paginate(20);
        return view('admin.pages.appointments',['appointments'=>$appointments]);
    }
    
}