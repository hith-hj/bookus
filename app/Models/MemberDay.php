<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberDay extends Model
{
    use HasFactory;
    protected $fillable =[
        'day',
        'start',
        'end',
        'admin_id'
    ];
    protected $hidden =[
        'created_at',
        'updated_at'
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
