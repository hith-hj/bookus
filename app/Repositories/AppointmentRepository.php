<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppointmentRepository {

    public function add(Request $request)
    {

    }

    public function update(Request $request, Appointment $appointment, $updateFromAppointment = true)
    {


    }

    public function delete(Appointment $appointment)
    {

    }
   public function getAppointments(Request $request){
    
    // check if admin or centetr
    $admin=Admin::find(auth('sanctum')->id());
    if($admin->is_admin == true)
{    return Appointment::query()->with(['appointmentServices','center','user']);}
    else
    {
        $center=$admin->center;
        return Appointment::query()->where('center_id',$center->id)->with(['appointmentServices','center','user']);}

   }

}
