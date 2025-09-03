<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;
    protected  $fillable=[
        'image',
        'description',
        'title'
        ,'center_id'
    ];
    protected $hidden=[
        'created_at',
        'updated_at'
    ];
    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}
