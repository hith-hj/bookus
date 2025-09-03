<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterService extends Model
{
    use HasFactory;
    protected $fillable =[
        'service_gender',
        'Duration',
        'price_type',
        'retail_price',
        'name',
        'center_id',
        'membership_id',
        'center_category_id',
        'Aftercare_description',
        'description',
    ];
    protected $hidden=[
        'created_at',
        'updated_at',
        'Treatment_type',
        'Aftercare_description',
        'online_booking',
        'extra_time',
        // 'description',
        "center_category_id",
        'status',
    ];
    protected $appends = ['new_price','offer'];
    protected $casts = [
        'retail_price' => 'integer',
        'offer'=>'integer',
    ];
    
    
    public function category()
    {
        return $this->belongsTo(CenterCategory::class);
    }
    
    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'center_service_offers','center_services_id','offer_id');
    }
    
    public function getNewPriceAttribute()
    {
        return $this->offer == 0 ? 0 : $this->retail_price - (($this->offers()->first()->value * 0.01) * $this->retail_price);
    }

    public function getOfferAttribute()
    {
        return  $this->offers()->count() == 0 || 
                $this->offers()->first()->status != 1 || 
                $this->offers()->first()->end_date < date('Y-m-d') ?
                    0 :
                    $this->offers()->first()->value ;
    }
    
    public function center(){
        return $this->belongsTo(Center::class);
    }
    
    
}
