<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterCategory extends Model
{
    use HasFactory;
    protected $fillable =["id","name",'image','status','center_id','is_main'];
    protected $hidden = [
        'created_at',
        'updated_at'
     ];
    protected $appends = ['services'];
    public function getServicesAttribute()
    {
    $services=CenterService::where([['center_category_id',$this->id],['center_id',$this->center_id]])->get();
    return $services;
    }
}
