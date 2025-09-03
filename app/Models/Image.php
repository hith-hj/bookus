<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable =['logo','images','center_id'];
    protected $hidden =['center_id'];
    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    protected $casts = [
        'images' => 'array'
    ];

}
