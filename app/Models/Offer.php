<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $fillable =['start_date','end_date','value','center_id','name','description','status'];
    protected $appends = ['appointments','total','count'];
    protected $casts = ['value'=>'integer'];
    
    public function services()
    {
        return $this->belongsToMany(CenterService::class, 'center_service_offers','offer_id','center_services_id');
    }
    
    public function getAppointmentsAttribute($type = 'total')
    {
        $count = 0;
        $total = 0;
        foreach($this->services as $key=>$service)
        {
            $temp = AppointmentServices::find($service->id) ?? false;
            if($temp !== false && !empty($temp) && !is_null($temp->appointment_id)){
                $appo = Appointment::find($temp->appointment_id,['total','user_id','created_at']);
                $count +=1;
                $total +=$appo->total;
            }
        }
        return ['total'=>$total,'count'=>$count];
    }
    
    public function getTotalAttribute()
    {
        return $this->appointments['total'];
    }
    
    public function getCountAttribute()
    {
        return $this->appointments['count'];
    }
    
    public static function checkOffersStatus($center_id)
    {
        $offers = self::where('center_id',$center_id)->get();
        foreach($offers as $offer)
        {
            if($offer->end_date < date('Y-m-d'))
            { 
                $offer->update(['status'=>-1,'description'=>'nonetryu']);
            }
        }
        return;
    }

}
