<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable =['id','key','value','center_id'];
    // protected $hidden =['center_id'];
    public function center()
    {
        return $this->belongsTo(Center::class);
    }

}
