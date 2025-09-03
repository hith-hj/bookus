<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;
    use Notifiable,HasApiTokens;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guard_name = 'web';
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'password',
        'cover_image',
        'token',
        'status',
        'address',
        'phone_number',
        'token',
        'is_admin',
        'center_id',
        'center_position',
        'member_desc',
        'firebase_token',
        'active_notification',
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'created_at',
        'updated_at'
    ];

    protected $casts = ['created_at'=>'date:Y-m-d'];
    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function memberAvailable(): HasMany
    {
        return $this->hasMany(MemberDay::class);
    }
    
    public function Notifications()
    {
        return $this->hasMany(Notification::class,'notifiable_id')->where('notifiable_type',get_class($this));
    }

    public function fullName(){
        return $this->first_name.' '.$this->last_name;
    }

    public function appointments(){
        return $this->hasMany(Appointment::class,'member_id');
    }

}
