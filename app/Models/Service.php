<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable =[
        'Treatment_type',
        'Aftercare_description',
        'service_gender',
        'online_booking',
        'Duration',
        'price_type',
        'retail_price',
        'extra_time',
        'name',
        'description',
        "category_id",
        'status'
    ];
    protected $hidden=[
        'created_at',
        'updated_at'
    ];



}
