<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable =[
        'center_id',
        'status',
        'total','member_id',
        'shift_start',
        'shift_end',
        'appointment_date',
        'using_by',
        'usingBy_id',
        'user_id',
        'total_time',
        'note'
    ];
    protected $hidden=[
    //   'created_at',
      'updated_at'
    ];

    const SHIFTS = [
       "9:00","9:30","10:00","10.30","11:00","11:30","12:00",
       "12:30","1:00","1:30","2:00","2:30","3:00","3:30","4:00",
       "4:30","5:00","5:30","6:00","6:30","7:00","7:30","8:00","8:30"
       ,"9:00","9:30","10:00"
    ];
    
    protected $casts = [
        'created_at'=>'date:Y-m-d', 
        'appointment_date'=>'date:Y-m-d',  
    ];
    protected $with=['user:id,first_name,last_name,email,image',];
    
    protected $appends = ['ref'];
    
    
    public function member()
    {
        return $this->belongsTo(Admin::class,'member_id');
    }
    
    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function appointmentServices()
    {
        return $this->hasMany(AppointmentServices::class);
    }
    
    public function getRefAttribute()
    {
      return $this->id .$this->user_id .$this->center_id;
    }
}
