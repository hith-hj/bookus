<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'status',
        'phone_number',
        'image',
        'latitude',
        'longitude',
        'email',
        'password',
        'address',
        'code',
        'firebase_token',
        'email_verified_at',
        'mobile_verified_at',
        'reset_token',
        'reset_verified',
        'is_verified',
        'token',
        'birth_date',
        'gender',
        'rate_app',
        'review_app',
        'active_notification',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'image',
        'address',
        'reset_token',
        'firebase_token',
        'reset_verified',
        'mobile_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'latitude' => 'double',
        'longitude' => 'double',
        'is_verified' => 'boolean',
        'active_notification' => 'boolean',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
    
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
    
    public function encryptPassword($password = null)
    {
        $this->password = bcrypt($password ?? $this->password);
        return $this;
    }
    
    public function generateActivationCode(): User
    {
        $this->is_verified=0;
        $this->code = rand( 1000 , 9999 );
        return $this;
    }

    public function activateUser(): User
    {
        $this->status=1;
        $this->is_verified=1;
        $this->mobile_verified_at = Carbon::now();
        $this->code = null;
        return $this;
    }
    
    public function passwordResetCode()
    {
        $this->reset_token = rand(1000,9999);
        $this->reset_verified = 'no';
        return $this;
    }
    
    public function clearPasswordResetCode()
    {
        $this->reset_token = null;
        $this->reset_verified = 'yes';
        return $this;
    }

    public function favorites()
    {
        return $this->belongsToMany(Center::class, 'favorites','user_id','center_id');
    }
    
    public function gifts()
    {
        return $this->belongsToMany(Center::class, 'gifts','user_id','center_id')
            ->withPivot('start_date',
                'end_date','value','duration');
    }
    public function reviews()
    {
        return $this->belongsToMany(Center::class, 'reviews','user_id','center_id')->withPivot('review',
            'rate');
    }
    // public function memberships()
    // {
    //     return $this->belongsToMany(Center::class, 'memberships','user_id','center_id')
    //         ->withPivot('start_date',
    //             'end_date','total','duration','name','description','terms');
    // }
    public function memberships()
    {
        return $this->belongsToMany(MemberShip::class, 'membership_user','user_id','membership_id')
            ->withPivot(['remaining','status'])
            ->withTimestamps();
    }
    public function setFirebaseToken($token): User
    {
        $this->firebase_token = $token;
        return $this;
    }
    public function userNotifications()
    {
        return $this->hasMany(Notification::class, 'notifiable_id')->where('notifiable_type',get_class($this));
    }

    public function fullName(){
        return $this->first_name.' '.$this->last_name;
    }

}
