<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
'address_title',
'apartment',
'latitude',
'longitude',
'postcode',
'district',
'default',
  'user_id'
    ];
    
    protected $casts = [
      'created_at'=>'date:Y-m-d',
    ];
}
