<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class MemberShip extends Model
{
    use HasFactory;

    protected $table='memberships';
    protected $fillable =['name','start_date','end_date','duration','description','terms','session','total','remaining','user_id','center_id'];
    protected $hidden =['user_id'];
    protected $appends = ['center_name','image'];
    protected $casts = [
        'created_at'=>'date:Y-m-d',
        'session' => 'integer',
        'remaining'=>'integer',
        'total'=>'double'
    ];
    public function services()
    {
        return $this->belongsToMany(CenterService::class, 'membership_services','membership_id','center_services_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class,'membership_user','membership_id','user_id')
        ->withPivot(['remaining','status'])
        ->withTimestamps();
    }
    
    // public function center()
    // {
    //     return $this->belongsTo(Center::class,'center_id','id');
    // }
    
    
    public function getCenterNameAttribute()
    {
        $center=Center::find($this->center_id);
        if( $center)
            return $center->name;
        else
            return "";
    }
    public function getImageAttribute()
    {
        $center=Center::find($this->center_id);
        if( $center)
            return $center->logo;
        else
            return "";
    }
}
