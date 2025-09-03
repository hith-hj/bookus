<?php

namespace App\Models;

use App\Http\Traits\FcmNotifiable;
use App\Http\Traits\SmsNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;
    use FcmNotifiable;
    use SmsNotification;

    protected $fillable = ['notifiable_id','notifiable_type','payload'];
    protected $appends = ['time'];
    protected $casts = ['payload'=>'array'];



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function getTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }
    public function setPayloadAttribute($value)
    {
        $this->attributes['payload'] = json_encode($value);
    }

}
