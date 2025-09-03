<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $fillable =['start_date','end_date','value','duration','remaining','center_id','status','user_id'];
    protected $hidden =['user_id'];
    protected $casts = ['value' => 'double','remaining'=>'double',];
    protected $appends = ['center_name'];
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    
    public function getCenterNameAttribute()
    {
        $center=Center::find($this->center_id);
        if( $center)
            return $center->name;
        else
            return "";
    }
    

}
