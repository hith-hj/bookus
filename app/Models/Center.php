<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Spatie\Permission\Traits\HasRoles;
use DB;

class Center extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'rated',
        'email',
        'status',
        'main_branch',
        'about',
        'latitude',
        'longitude',
        'currency',
        'city',
        'country',
        'phone_number',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at'
    ];
    
    protected $casts = ['created_at'=>'date:Y-m-d',];

    protected $with=[
        'admins:id,first_name,last_name,center_id,email,member_desc,cover_image,center_position,is_admin',
        'categories:id,name,center_id,image',
        'centerServices',
        'contacts',
        'days',
        'review'
    ];
    protected $appends = ['user_distance', 'main_category','logo','rate','number_reviews','is_offer','center_images','is_fullFilled','branches',];
    
    public function review()
    {
        return $this->hasMany(Review::class);
    }
    
    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
    public function days(): HasMany
    {
        return $this->hasMany(OpenDay::class);
    }
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }
    
    public function centerServices()
    {
        return $this->hasMany(CenterService::class)->limit(20);
    }

    public function resources(): hasMany
    {
        return $this->hasMany(Resource::class, 'center_id');
    }
    public function images(): HasOne
    {
        return $this->hasOne(Image::class, 'center_id');
    }
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'center_id');
    }
    public function categories(): HasMany
    {
        return $this->hasMany(CenterCategory::class, 'center_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(CenterService::class, 'center_id');
    }
    public function centerPermissions()
    {
        return $this->belongsToMany(TeamPermission::class, 'center_permissions')->withPivot(['basic','low','medium','high']);
    }
    public function reviews()
    {
        return $this->belongsToMany(User::class, 'reviews','center_id','user_id')
            ->withPivot('review','rate');
    }
    public function gifts()
    {
        return $this->belongsToMany(User::class, 'gifts','center_id','user_id')
            ->withPivot('start_date', 'end_date','value','duration');
    }

    public function memberships()
    {
        return $this->belongsToMany(User::class, 'memberships','center_id','user_id')
            ->withPivot('start_date','end_date','total','duration','name','description','terms');
    }
    
    public function cMemberships(){
        return $this->hasMany(MemberShip::class);
    }
    
    public function cGifts(){
        return $this->hasMany(Gift::class);
    }
    
    public function getMainCategoryAttribute()
    {
        $mainCategory=CenterCategory::Where('center_id',$this->id)->where('is_main',1)->first();
        return is_null($mainCategory) ? "Category" : $mainCategory->name;
    }
    
    public function getLogoAttribute()
    {
        $mainImage=Image::Where('center_id',$this->id)->first();
        return is_null($mainImage) ? null : $mainImage->logo;
    }
    public function getcenterImagesAttribute()
    {
        $images = Image::Where('center_id',$this->id)->first();
        //this is changed check the app
        return $this->center_images = $images != null ? $images->images : null;
    }
    public function getRateAttribute()
    {
        $this->makeHidden(['reviews']);
        if($this->reviews()->count() < 1){
            return 0;
        }else{
            return ceil($this->reviews()->sum('rate')/$this->reviews()->count());
        }
    }
    
    public function getNumberReviewsAttribute()
    {
        $this->with('reviews');

        $count=0;
        foreach ($this->reviews as $review)
        {
            $count++;
        }
        $this->makeHidden(['reviews']);

            return $count;
    }
    public function getIsOfferAttribute()
    {
        $offers=Center::where('id',$this->id)->with('offers')->first();
        if(isset($offers) && count( $offers->offers)==0)
            return 0;
        else
            return 1;

    }
    
    public function getIsFullFilledAttribute()
    {
        if(count($this->centerServices)<=0  || count($this->contacts)<=0 || count($this->categories)<=0 || count($this->days)<=0){
            return $this->is_fullFilled = false;
        }
        return $this->is_fullFilled = true;
    }
    
    public function getBranchesAttribute()
    {
        return Center::where('main_branch',$this->id)->get();
    }
    
    public function getUserDistanceAttribute() {
        $user = auth()->user();
        if(!is_null($user) && get_class($user) == 'App\Models\User' && $user->addresses()->count() > 0){
            $rad = M_PI / 180;
            $location = $user->addresses()->where('default',1)->first();
            return ceil(acos(
              sin($this->latitude*$rad) * sin($location->latitude*$rad) 
              + cos($this->latitude*$rad) * cos($location->latitude*$rad) 
              * cos($this->longitude*$rad - $location->longitude*$rad)) * 6371);// Kilometers
        }
        return -1;
    }

    public function settings(){
        return DB::table('center_settings')->where('center_id',$this->id)->get() ?? [];
    }

    public function admin(){
        return Admin::where([['center_id',$this->id],['center_position','owner'],['is_admin',1]])->first();
    }

}
