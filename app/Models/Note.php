<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    protected  $fillable=[
        'center_id',
        'user_id',
        'note'
    ];
    protected $hidden=[
      'updated_at'
    ];
    protected $casts = ['created_at'=>'date:Y-m-d',];


}
