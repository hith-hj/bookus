<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\carbon;

class AppointmentServices extends Model
{
    use HasFactory;
    protected $table='appointment_services';
    protected $fillable =[
        'appointment_id',
        'title',
        'price',
        'center_services_id'
    ];
    protected $hidden=[
      'created_at',
      'updated_at',
    //   'price',
    ];
    
    // protected $appends = ['service'];

    
    protected $casts = [
        // 'created_at'=>'date:Y-m-d',
        'price'=>'integer',
        'center_services_id' => 'integer'
    ];
    

    
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
    
    public function getServiceAttribute()
    {
        return CenterService::where('id', $this->center_services_id)->first(['id','retail_price']);
    }

}







// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class AppointmentServices extends Model
// {
//     use HasFactory;
//     protected $table='appointment_services';
//     protected $fillable =[
//         'appointment_id',
//         'title',
//         'price',
//         'appointment_id',
//         'center_services_id'
//     ];
//     protected $hidden=[
//       'created_at',
//       'updated_at'
//     ];
//     protected $appends = ['new_price','discount'];

//     public function appointment()
//     {
//         return $this->belongsTo(Appointment::class);
//     }
//     protected $casts = [
//         'price' => 'double',
//     ];
//     public function getNewPriceAttribute()
//     {
//         $offers=CenterService::where('id',$this->center_services_id)-> with('offers')->first();


//         if(count( $offers->offers)==0)
//             return (double)$this->price;
//         else
//             return  (double)($this->price - (($offers->offers[0]->value/100)*$this->price));
//     }

//     public function getDiscountAttribute()
//     {
//         $offers=CenterService::where('id',$this->center_services_id)-> with('offers')->first();


//         if(count( $offers->offers)==0)
//             return (int)0;
//         else
//             return (int)($offers->offers[0]->value);
//     }
// }





