<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{

    use HasFactory;
    protected $fillable =[
        'terms_use',
        'terms_services',
        'privacy_policy',
        'about_us'
    ];
    protected $hidden =[
        'created_at',
        'updated_at'
    ];

}
