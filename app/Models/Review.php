<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected  $fillable=[
        'review',
        'rate',
        'user_id'
        ,'center_id'
    ];
    protected $hidden=['updated_at'];
    protected $casts = ['created_at'=>'date:Y-m-d',];
    protected $with = ['user:id,first_name,last_name,image'];
    protected $appends = ['formated_date']; 
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getFormatedDateAttribute()
    {
        return $this->created_at?->diffForHumans();
    }

}
