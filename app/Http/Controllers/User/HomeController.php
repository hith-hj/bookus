<?php 

namespace App\Http\Controllers\User;

use App\Models\Admin;
use App\Models\Notification;
use App\Models\User;
use App\Models\Center;
use App\Models\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class HomeController extends Controller
{

    public function home(){
        $centers = Center::paginate(20);
        $offers = Center::query()->withWhereHas("offers")->get(['id','name','rated'])->take(6);
        $featured = Center::query()->where('rated','>=',4)->get(['id','name','rated'])->sortByDesc('rated')->take(3);
        return view('user.pages.home',['centers'=>$centers,'featured'=>$featured,'offers'=>$offers]);
    }
    
    public function appointments(){
        $appointments = auth()->user()->appointments()->with(['center:id,name','appointmentServices'])->get();
        $booked = $appointments->filter(function($appointment){
            return $appointment->status == 'booked';
        });
        $appointments = $appointments->filter(function($appointment){
            return $appointment->status != 'booked';
        });
        // dd($booked,$appointments);
        return view('user.pages.appointments',['booked'=>$booked,'appointments'=>$appointments]);
    }
    
    public function favorites(){
        return view('user.pages.favorits',['favorits'=>auth()->user()->favorites]);
    }
    
    
    
    
}