<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenDay extends Model
{
    use HasFactory;
    protected $fillable =[
        'day',
        'open',
        'close',
        'center_id'
    ];
    protected $hidden =[
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'open'=> 'date: H:i',
        'close'=> 'date: H:i',
    ];
    
    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}
