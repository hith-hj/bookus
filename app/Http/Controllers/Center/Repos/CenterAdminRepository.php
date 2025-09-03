<?php

namespace App\Http\Controllers\Center\Repos;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Closure;
use Carbon\carbon;

use App\Models\Image;
use App\Models\Center;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Models\Contact;
use App\Models\OpenDay;
use App\Models\Admin;
use App\Models\Offer;
use App\Models\Resource;
use App\Models\MemberDay;
use App\Models\Setting;
use App\Models\Notification;


class  CenterAdminRepository
{
    private const contacts = ['telegram', 'whatsapp', 'x', 'facebook', 'instagram', 'youtube','phone_one','phone_two'];
    private const days = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
    private $settingsToStore=[
            'create_appointments'=>true,
            'cancel_appointments'=>true,
            'reschedule_appointments'=>true,
            'cancelation_range'=>6,
            'member_selection'=>true,
        ];

    public function addCenterDetails($request)
    {
        $center = new Center(populateModelData($request, Center::class));
        $center->status = 0;
        $center->save();
        $admin = auth()->user();
        $admin->center_id = $center->id;
        $admin->save();
        if ($request->hasFile('logo') || $request->hasFile('images')) {
            $image = new Image(populateModelData($request, Image::class));
            if($request->hasFile('logo')){
                $image->logo = Storage::disk('public')->put('center_logo', $request->file('logo'));
            }
            if ($request->hasFile('images')) {
                $image->images = uploadMultiImages("images", "centers");
            }
            $image->center()->associate($center->id);
            $image->save();
        }
        
        foreach($this->settingsToStore as $key=>$value){
            DB::table('center_settings')->insert([
                'center_id'=>$center->id,
                'key'=>$key,
                'value'=>$value,
            ]);
        }

        return ['stat' => true, 'center' => $center];
    }

    public function centerUpdateDetails($request, $center)
    {
        $center->update(populateModelData($request, Center::class));
        $images_obj = $center->images;
        if ($request->hasFile('logo')) {
            if ($images_obj->logo != null) {
                $images_obj->logo = Storage::disk('public')->delete($images_obj->logo);
            }
            $images_obj->logo = Storage::disk('public')->put('center_logo', $request->file('logo'));
            $images_obj->save();
        }
        $images = $center->images->images;
        if ($request->has('images')) {
            if(isset($images) && !is_null($images)){
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
                $images_obj->images = null;
            }
            $photos = uploadMultiImages("images", "centers");
            $images_obj->images = $photos;
            $images_obj->save();
        }

        return ['stat' => true, 'center' => $center];
    }

    public function addCenterCategories($request)
    {
        $center = Center::findOrFail($request->center_id);
        foreach($request->category as $key => $val){
            $category = Category::find($key);
            CenterCategory::updateOrCreate(
                ['center_id'=>$center->id,'name'=>$val],
                [
                    'image'=>$category->image,
                    'status'=>1,
                ]);
        }
        return ['stat'=>true,'payload'=>$center->categories];
    }
    
    public function addCenterServices($request)
    {
        $center = Center::findOrFail($request->center_id);
        if($request->price_type == 'free'){
            $request['retail_price'] = 0;
        }
        $centerService = CenterService::updateOrCreate(
            ['center_id'=>$center->id,'center_category_id'=>$request->center_category_id,'name'=>$request->name],
            $request->all());
        return ['stat'=>true,'payload'=>$centerService];
    }

    public function addCenterDays($request)
    {
        $center =  Center::find($request->center_id);
        if (!$center){
            return ['stat'=>false,'payload'=>'Center Not Found'];
        }
        foreach($request->days as $key=>$item){
            $day = OpenDay::updateOrCreate(
                ['day' => $key, 'center_id' => $center->id],
                [
                    'open' => $item['open'],
                    'close' => $item['close'],
                ]
            );
            $day->center()->associate($center->id);
            $day->save();
        }
        return ['stat'=>true,'payload'=>$center->days];
    }
    
    public function centerAvailableContacts($request)
    {
        $static_contacts = self::contacts;
        $contacts = Contact::where('center_id',$request->center_id)->get(['key']);
        foreach($contacts as $contact){
            if($key = array_search(strtolower($contact->key),$static_contacts)){
                unset($static_contacts[$key]);
            }
        }
        return array_values($static_contacts);
    }

    public function addCenterContact($request)
    {
        $center =  Center::find($request->center_id);
        if (!$center) return ['stat'=>false,'payload'=>'Center Not Found'];
        foreach (self::contacts as $key) {
            if ($request->has($key)) {
                Contact::updateOrCreate(
                    ['key'=>$key,'center_id'=>$center->id],
                    ['value' => $request->$key,]
                );
            }
        }
        return ['stat'=>true,'payload'=>$center->contacts];
    }  
    
    public function getCenterMembers($request)
    {
        $request->validate(['center_id'=>['required','exists:centers,id']]);
        $members = Admin::where([['center_id',$request->center_id],['is_admin',0]])
        ->when($request->filled('orderBy') && !$request->filled('member_id'),
            function($members) use ($request){
                foreach($request->orderBy as $key=>$value){
                    if($key=="total" || $value == null)
                    {break;}
                    if(Schema::hasColumn('admins', $key) && in_array($value ,['desc','asc'])){
                        $members->orderBy($key,$value);  
                    }
                }
            })
        ->when($request->filled('member_id') && !$request->filled('orderBy'),
            function($members) use ($request){
                $members->where('id',$request->member_id);
            })
        ->get(['id','first_name','last_name','center_position','created_at','email','phone_number','member_desc','cover_image']);
        foreach($members as $member)
        {
            $member->days = MemberDay::where('admin_id',$member->id)->get();
            $member->services = CenterService::
                whereIn('id',
                    DB::table('member_services')
                    ->where('member_id',$member->id)
                    ->pluck('service_id')
                    )
                ->get(['id','name','Duration','retail_price']);
            foreach($member->services as $service)
            {
                $service->relation_id = DB::table('member_services')
                    ->where([['member_id',$member->id],['service_id',$service->id]])->first()->id;
            }
            $member->holidays = DB::table('member_holidays')->where('member_id',$member->id)->get();
            foreach($member->holidays as $holiday)
            {
                $holiday->name = DB::table('center_holidays')->where('id',$holiday->holiday_id)->first()->name;
            }
        }
        $stat = count($members)>0 ? 
            ['stat'=>true,'message'=>'success','payload'=>$members] :
            ['stat'=>false,'message'=>'Error','payload'=>'No Members Found'];
        return $stat;
    }
    
    public function addCenterMember($request)
    {
        $admin = Admin::firstOrNew([
                'email'=>$request->email,
            ],[
                'first_name'=>$request->first_name,                
                'last_name'=>$request->last_name,                
                'password'=>bcrypt($request->password),                
                'status'=>1,                
                'is_admin'=>0,  
                'center_id'=>$request->center_id,
                'center_position'=>$request->center_position,
                'member_desc'=>$request->member_desc,
            ]);
        $admin->save();
        $admin->assignRole('center');
        if($request->filled('days')){
            foreach($request->days as $key){
                $day = OpenDay::find($key);
                if($day->center_id == $request->center_id){
                  $memberDay = MemberDay::create([
                    'day'=>$day->day,
                    'start'=>$day->open,
                    'end'=>$day->close,
                    'admin_id'=>$admin->id
                    ]);  
                }else{
                    return ['stat'=>false,'message'=>'Day Error','payload'=>'Day not found'];
                }
            }
        }
        if($request->filled('services')){
            foreach($request->services as $id){
                $service = CenterService::find($id);
                if($service->center_id == $request->center_id){
                    $memberService = DB::table('member_services')
                    ->insert([
                        'member_id'=>$admin->id,
                        'service_id'=>$id,
                    ]);   
                }else{
                    return ['stat'=>false,'message'=>'service Error','payload'=>'service not found'];
                }
            }
        }
        // $token = $admin->createToken('API');
        return ['stat'=>true,'message'=>'Member Created','payload'=>$admin];
    }
    
    public function deleteCenterMember($request)
    {
        $request->validate(['center_id'=>['required','exists:centers,id'],'member_id'=>['required','exists:admins,id',]]);
        $member = Admin::find($request->member_id);
        if($member->center_id != $request->center_id 
        || auth()->user()->center_id != $request->center_id 
        || auth()->user()->center_position != 'owner'
        || $member->status != 1){
            return ['stat'=>false,'message'=>'Error','payload'=>'Unauthorized Action'];
        }
        $member->status = -1;
        $member->center_id = null;
        $member->save();
        return ['stat'=>true,'message'=>'Success','payload'=>'Member Deleted'];
    }
    
    public function getCenterOffers($request)
    {
        if(!$request->has('center_id')){
            return ['stat'=>false,'message'=>'Error','payload'=>'Center id is required'];
        }
        $center = center::findOrFail($request->center_id);
        if(!$center){
            return ['stat'=>false,'message'=>'Error','payload'=>'Center is Not Found'];
        }
        $offers = Offer::with(['services:id,name,retail_price',])
                ->where('center_id',$center->id)
                ->when($request->has('offer_id') && !is_null($request->offer_id), 
                function($offers) use($request) {
                    $offers->where('id',$request->offer_id);})
                ->when($request->has('filters') && !$request->has('offer_id'),
                function($offers) use($request){
                    foreach($request->filters as $key => $value){
                        if (Schema::hasColumn('offers', $key)){
                            $offers->where($key,$value);   
                        }
                    }})
                ->when($request->has('orderBy') && !$request->has('offer_id'),
                function($offers) use ($request){
                    foreach($request->orderBy as $key => $value){
                        if($key=="total" || $value == null)
                        {break;}
                        if (Schema::hasColumn('offers', $key) && in_array($value ,['desc','asc'])){
                            $offers->orderBy($key,$value);  
                        }
                    }
                })->get();
        if($request->has('orderBy') && in_array('total', array_keys($request->orderBy))){
            $offers = $offers->sortBy([['total',$request->orderBy['total']],]);
        }
        return count($offers)>0 ? 
            ['stat'=>true,'message'=>'Offers','payload'=>$offers] : 
            ['stat'=>false,'message'=>'Error','payload'=>'No Offers Yet'];
    }
    
    public function addCenterOffer($request)
    {
        $validator = $this->validateOfferDetails($request->all());
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $offer = Offer::create($request->all());
        foreach($request->services as $id){
            $service = CenterService::find($id);
            $serviceOffer = DB::table('center_service_offers')->updateOrInsert([
                    'center_services_id'=>$service->id,
                    'offer_id'=>$offer->id,
                ],[
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);   
        }
        return ['stat'=>true,'message'=>'Offer Created','payload'=>$offer];
    }
    
    public function deleteCenterOffer($request)
    {
        if(!$request->has('offer_id') || !$request->has('center_id')){
            return ['stat'=>false,'message'=>'Error','payload'=>'Missing Required Info'];
        }
        $offer = Offer::find($request->offer_id);
        if(!$offer || $offer->center_id != $request->center_id){
            return ['stat'=>false,'message'=>'Error','payload'=>'Offer is not found !'];
        }
        $offer->delete();
        return ['stat'=>true,'message'=>'Success','payload'=>'offer is deleted'];
    }
    
    public function deactivateCenterOffer($request)
    {
        if(!$request->has('offer_id') || !$request->has('center_id')){
            return ['stat'=>false,'message'=>'Error','payload'=>'Missing Required Info'];
        }
        $offer = Offer::find($request->offer_id);
        if(!$offer || $offer->center_id != $request->center_id ){
            return ['stat'=>false,'message'=>'Error','payload'=>'Offer is not found'];
        }
        if($offer->end_date < date('Y-m-d'))
        {
            return ['stat'=>false,'message'=>'Error','payload'=>'Offer is expired you have to make new one'];
        }
        $offer->status = $offer->status == -1 ? 1 : -1;
        $offer->save();
        return ['stat'=>true,'message'=>'Success','payload'=>$offer->status = -1 ? 'Offer is Deactivated':'Offer is Activated'];
    }
    
    public function getCenterBranches($request)
    {
        if (!$request->has('center_id')) {
            return ['stat' => false, 'message' => 'Error', 'payload' => 'Center id is required'];
        }
        $center = center::find($request->center_id);
        if (!$center) {
            return ['stat' => false, 'message' => 'Error', 'payload' => 'Center is Not Found'];
        }
        $centers = Center::where('main_branch', $center->id)
            ->when($request->has('branch_id') && !is_null($request->branch_id),
                function ($centers) use ($request) {
                    $centers->where('id', $request->branch_id);
                }
            )
            ->when($request->has('filters') && !$request->has('branch_id'),
                function ($centers) use ($request) {
                    foreach ($request->filters as $key => $value) {
                        if (Schema::hasColumn('centers', $key)) {
                            $centers->where($key, $value);
                        }
                    }
                })
            ->when($request->has('orderBy') && !$request->has('branch_id'),
            function ($centers) use ($request) {
                foreach ($request->orderBy as $key => $value) {
                    if ($key == "total" || is_null($value)) {
                        break;
                    }
                    if (Schema::hasColumn('centers', $key) && in_array($value, ['desc', 'asc'])) {
                        $centers->orderBy($key, $value);
                    }
                }
            })->get();
        return count($centers) > 0 ?
            ['stat' => true, 'message' => 'Centers', 'payload' => $centers] :
            ['stat' => false, 'message' => 'Error', 'payload' => 'No Centers Yet'];
    }
    
    public function addCenterBranch($request)
    {
        $centerValidator = $this->validateCenterDetails($request->all());
        if($centerValidator->fails()){
            return ['stat'=>false,'message'=>$centerValidator->errors()->first(),'payload'=>$centerValidator->errors()];
        }
        $adminValidator = $this->validateAdminDetails($request->all());
        if($adminValidator->fails()){
            return ['stat'=>false,'message'=>$adminValidator->errors()->first(),'payload'=>$adminValidator->errors()];
        }
        $main_branch = Center::find($request->center_id);
        $center = new Center(populateModelData($request, Center::class));
        $center->status = 0;
        $center->main_branch = $main_branch->id ?? auth()->user()->center_id;
        $center->save();

        if ($request->hasFile('logo') || $request->hasFile('images')) {
            $image = new Image(populateModelData($request, Image::class));
            if($request->hasFile('logo')){
                $image->logo = Storage::disk('public')->put('center_logo', $request->file('logo'));
            }
            if ($request->hasFile('images')) {
                $image->images = uploadMultiImages("images", "centers");
            }
            $image->center()->associate($center->id);
            $image->save();
        }
        
        $admin = Admin::create([
                'email'=>$request->admin_email,
                'first_name'=>$request->admin_first_name,                
                'last_name'=>$request->admin_last_name,                
                'password'=>bcrypt($request->admin_password),                
                'status'=>1,                
                'is_admin'=>1,
                'center_id'=> $center->id,
                'center_position'=>'owner',
                'member_desc'=>$request->member_desc,
            ]);
        return ['stat' => true,'message'=>'Branch','payload' => ['center'=>$center,'admin'=>$admin]];
    }
    
    public function editCenterBranch($request)
    {
        $center = Center::find($request->center_id);
        $admin = Admin::find($request->admin_id);
        $main_branch = Center::find($request->main_branch);
        if(!$admin || !$main_branch || !$center)
        {
            return ['stat'=>false,'message'=>'Error','payload'=>'Missing reuired Information'];
        }
        if($center->main_branch != $main_branch->id || $admin->center_id != $center->id)
        {
            return ['stat'=>false,'message'=>'Error','payload'=>'Unauthorized'];
        }
        
        $center_validator = $this->validateCenterDetailsUpdate($request->all());
        if($center_validator->fails()){
            return ['stat'=>false,'message'=>$center_validator->errors()->first(),'payload'=>$center_validator->errors()];
        }
        $validator = $this->validateAdminDetails($request->all());
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        
        $center->update($center_validator->validated());

        if ($request->hasFile('logo') || $request->hasFile('images')) {
            $image = new Image(populateModelData($request, Image::class));
            if($request->hasFile('logo')){
                $image->logo = Storage::disk('public')->put('center_logo', $request->file('logo'));
            }
            if ($request->hasFile('images')) {
                $image->images = uploadMultiImages("images", "centers");
            }
            $image->center()->associate($center->id);
            $image->save();
        }
        $admin->update([
                'first_name'=>$request->admin_first_name,                
                'last_name'=>$request->admin_last_name, 
                'member_desc'=>$request->member_desc,
            ]);
        return ['stat' => true,'message'=>'Branch','payload' => ['center'=>$center,'admin'=>$admin]];
    }
    
    
    public function deleteCenterBranch($request)
    {
        if (!$request->filled('center_id') || !$request->filled('branch_id')) {
            return ['stat' => false, 'message' => 'Error', 'payload' => 'Missing required information'];
        }
        $center = Center::find($request->center_id);
        $branch = Center::find($request->branch_id);
        if (!$center) {
            return ['stat' => false, 'message' => 'Error', 'payload' => 'Center is Not Found'];
        }  
        if( !$branch || $branch->status == -1 || $branch->main_branch != $center->id){
            return ['stat' => false, 'message' => 'Error', 'payload' => 'Branch is Not Found'];
        }
        $branch->status = -1;
        $branch->save();
        return ['stat' => true, 'message' => 'Success', 'payload' => 'Branch Deleted'];
    }
    
    public function getCenterHolidays($request)
    {
        if(! $request->filled('center_id'))
        {
            return ['stat'=>false,'message'=>'Error','payload'=>'Center id is required'];
        }
        
        $holidays = DB::table('center_holidays')->where('center_id',$request->center_id)->orderBy('created_at','asc')->get();
        $payload = count($holidays)>0 ? $holidays :
            [
                ['id'=>1,'center_id'=>(int)$request->center_id,'name'=>'Annual','created_at'=>now()],
                ['id'=>2,'center_id'=>(int)$request->center_id,'name'=>'Sick','created_at'=>now()],
            ];
        return ['stat'=>true,'message'=>'success','payload'=>$payload];
    }
    
    public function createCenterHoliday($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'name' => ['required','string'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        } 
        if(DB::table('center_holidays')->where('center_id',$request->center_id)->count() == 0){
            DB::table('center_holidays')->insert([
                [
                    'center_id'=>$request->center_id,
                    'name'=>'Annual',
                    'created_at'=>now(),
                ],
                [
                    'center_id'=>$request->center_id,
                    'name'=>'Sick',
                    'created_at'=>now(),
                ]
            ]);
        }
        $holiday = DB::table('center_holidays')->when(
            ! DB::table('center_holidays')->where([
                ['center_id',$request->center_id],
                ['name',$request->name],
                ])->exists()
            ,function(Builder $query)use($request){
                $query->insert([
                'center_id'=>$request->center_id,
                'name'=>$request->name,
                'created_at'=>now(),
                ]);
        });
        return ['stat'=>true,'message'=>'success','payload'=>'Holiday Created'];
    }
    
    public function deleteCenterHoliday($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'holiday_id' => ['required','exists:center_holidays,id'],
            ]);
        if($validator->fails())
        {
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $holiday = DB::table('center_holidays')->where('id',$request->holiday_id);
        if($holiday->first()->center_id != $request->center_id)
        {
            return ['stat'=>false,'message'=>'Error','payload'=>'Unauthorized'];    
        }
        $holiday->delete();
        return ['stat'=>true,'message'=>'success','payload'=>'Deleted Successfuy'];
    }
    
    // public function getMembersholidays($request)
    // {
    //     $validator = Validator::make($request->all(),[
    //         'center_id'=>['required','exists:centers,id'],
    //         ]);
    //     if($validator->fails())
    //     {
    //         return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
    //     }
    //     $center_holidays = DB::table('center_holidays')
    //         ->where('center_id',$request->center_id)
    //         ->where(function(Builder $query){
    //             $query
    //                 ->select('id')
    //                 ->from('member_holidays')
    //                 ->whereColumn('member_holidays.holiday_id','center_holidays.id')
    //                 ->count();
    //         },'>',0)
    //         ->dd();
    //         // ->get(['id','name']);
    //     foreach($center_holidays as $key=>$center_holiday)
    //     {
    //         if(DB::table('member_holidays')->where('holiday_id',$center_holiday->id)->count() > 0)
    //         {
    //             $center_holiday->members_holidays = DB::table('member_holidays')
    //                 ->where('holiday_id',$center_holiday->id)
    //                 ->get(['id','start_date','end_date','note']);
    //             foreach($center_holiday->members_holidays as $member_holiday)
    //             {
    //                 $member_holiday->member = Admin::where('id',$member_holiday->id)->first(['first_name','last_name']);
    //             }
    //         }else{
    //             unset($center_holidays[$key]);
    //         }
    //     }
    //     return ['stat'=>true,'message'=>'Success','payload'=>$center_holidays];
    // }
    
    public function createMemberHoliday($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'holiday_id' => ['required','exists:center_holidays,id'],
            'member_id' => ['required','exists:admins,id'],
            'start_date' => ['required','date_format:Y-m-d'],
            'duration' => ['required','numeric','between:1,30'],
            'note'=>['nullable','string','max:100'],
            ]);
        if($validator->fails())
        {
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $member_holiday = DB::table('member_holidays')->when(
            ! DB::table('member_holidays')->where([
                ['member_id',$request->member_id],
                ['holiday_id',$request->holiday_id],
                ['start_date',$request->start_date],
                ['duration',$request->duration],
                ])->exists()
            ,function(Builder $query) use ($request){
                $query->insert([
                    'holiday_id'=>$request->holiday_id,
                    'member_id'=>$request->member_id,
                    'start_date'=>$request->start_date,
                    'end_date'=>Carbon::parse($request->start_date)->addDay($request->duration),
                    'duration'=>$request->duration,
                    'note'=>$request->note ?? null,
                ]);
            }
        );
        return ['stat'=>true,'message'=>'success','payload'=>'Holiday Created'];   
    }
    
    public function deleteMemberHoliday($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'holiday_id' => ['required','exists:member_holidays,id'],
            'member_id' => ['required','exists:admins,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $holiday = DB::table('member_holidays')->where('id',$request->holiday_id);
        $member = Admin::find($request->member_id);
        if($holiday->first()->member_id != $member->id || $member->center_id != $request->center_id){
            return ['stat'=>false,'message'=>'error','payload'=>'Holiday can not be deleted'];
        }
        $holiday->delete();
        return ['stat'=>true,'message'=>'success','payload'=>'Holiday Deleted'];   
    }
    
    public function addMemberService($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'member_id' => ['required','exists:admins,id'],
            'services' => ['required','array','min:1'],
            'services.*' => ['required','exists:center_services,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $member = Admin::find($request->member_id);
        $center = Center::find($request->center_id);
        if($member->status != 1 || $center->status != 1 ){
            return ['stat'=>false,'message'=>'Error','payload'=>'this operation can not be done'];
        }
        if($member->center_id != $center->id ){
            return ['stat'=>false,'message'=>'Error','payload'=>'Unauthorized'];
        }
        
        $query = DB::table('member_services');
        $member_services = $query->where('member_id',$member->id)->pluck('service_id')->toArray();
        foreach($request->services as $value)
        {
            $service = CenterService::find($value);
            if(! in_array($service->id,$member_services) && $service->center_id == $center->id){
                $new_service = $query->insert([
                        'member_id'=>$member->id,
                        'service_id'=>$service->id,
                    ]);
            }   
        }
        return ['stat'=>true,'message'=>'success','payload'=>'Service Added'];
    }
    
    public function deleteMemberService($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'member_id' => ['required','exists:admins,id'],
            'service_id' => ['sometimes','required','exists:member_services,id'],
            // 'services' => ['sometimes','required','array','min:1'],
            // 'services.*' => ['required','exists:center_services,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $member = Admin::find($request->member_id);
        $center = Center::find($request->center_id);
        $member_service = DB::table('member_services')->where('id',$request->service_id);
        if($member->status != 1 || $member->center_id != $center->id || $member_service->first()->member_id != $member->id){
            return ['stat'=>false,'message'=>'Error','payload'=>'Unauthorized'];
        }
        if(DB::table('member_services')->where('member_id',$member->id)->count() <= 1){
            return ['stat'=>false,'message'=>'Error','payload'=>'Service can not be deleted '];
        }
        $member_service->delete();
        return ['stat'=>true,'message'=>'success','payload'=>'Service Deleted'];
    }
    
    public function addMemberDay($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'member_id' => ['required','exists:admins,id'],
            'service_id' => ['sometimes','required','exists:member_services,id'],
            // 'services' => ['sometimes','required','array','min:1'],
            // 'services.*' => ['required','exists:center_services,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $member = Admin::find($request->member_id);
        $center = Center::find($request->center_id);
        $member_days = MemberDay::where('admin_id',$member->id)->pluck('day')->toArray();
        foreach($request->days as $key){
            $day = OpenDay::find($key);
            if($day->center_id == $request->center_id && ! in_array($day->day,$member_days)){
              $memberDay = MemberDay::create([
                'day'=>$day->day,
                'start'=>$day->open,
                'end'=>$day->close,
                'admin_id'=>$member->id
                ]);  
            }
        }
        
        return ['stat'=>true,'message'=>'success','payload'=>'Day Added'];
    }
    
    public function deleteMemberDay($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'member_id' => ['required','exists:admins,id'],
            'day_id' => ['sometimes','required','exists:member_days,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $member = Admin::find($request->member_id);
        $center = Center::find($request->center_id);
        $member_day = MemberDay::find($request->day_id);
        if($member->status != 1 || $member->center_id != $center->id || $member_day->admin_id != $member->id){
            return ['stat'=>false,'message'=>'Error','payload'=>'Unauthorized'];
        }
        if(MemberDay::where('admin_id',$member->id)->count() <= 1){
            return ['stat'=>false,'message'=>'Error','payload'=>'Day can not be deleted '];
        }
        $member_day->delete();
        return ['stat'=>true,'message'=>'success','payload'=>'Day Deleted'];
    }
    
    
    public function getCenterResources($request)
    {
        if(!$request->filled('center_id')){
            return ['stat'=>false,'message'=>'Error','payload'=>'missing required information'];
        }
        $center = Center::find($request->center_id);
        if(!$center){
            return ['stat'=>false,'message'=>'Error','payload'=>'center is not found'];
        }
        return ['stat'=>true,'message'=>'Success','payload'=>Resource::where('center_id',$center->id)->get()];
    }
    
    
    public function createCenterResource($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
                'title'=>['required','string',],
                'description'=>['required','string','max:300'],
                'image'=>['required','image','max:512'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        
        $resource = Resource::create([
                'title'=>$request->title,
                'description'=>$request->description,
                'center_id' => $request->center_id
            ]);
            
        if ($request->hasFile('image')){
            $resource->image = Storage::disk('public')->put('resourcees', $request->file('image'));
        }
        
        $resource->save();
        return ['stat'=>true,'message'=>'success','payload'=>$resource];
    }
    public function deleteCenterResource($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
                'resource_id'=>['required','exists:resources,id',],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        
        $resource = Resource::find($request->resource_id);
        if ($request->center_id != $resource->center_id){
            return ['stat'=>false,'message'=>'error','payload'=>'Unauthorized'];
        }
        $resource->delete();
        return ['stat'=>true,'message'=>'success','payload'=>'resource deleted'];
    }
    
    public function getCenterDaysoff($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $days = DB::table('center_daysoff')->get();
        return ['stat'=>true,'message'=>'Success','payload'=>$days];
    }
    
    public function createCenterDayoff($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'start_date'=>['required','date_format:Y-m-d'],
            'end_date'=>['required','date_format:Y-m-d'],
            'description'=>['required','string','max:300'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        
        $day = DB::table('center_daysoff')->insert([
            'center_id'=>$request->center_id,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'description'=>$request->description,
            ]);
        return ['stat'=>true,'message'=>'Success','payload'=>'created'];
    }
    
    public function deleteCenterDayoff($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'days_id'=>['required','exists:center_daysoff,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $day = DB::table('center_daysoff')->where('id',$request->days_id);
        if(!$day->first() || $day->first()->center_id != $request->center_id){
            return ['stat'=>false,'message'=>'Error','payload'=>'center not found'];
        }
        $day->delete();
        return ['stat'=>true,'message'=>'Success','payload'=>'deleted'];
    }
    
    public function getCenterCancelationReasons($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $reasons = DB::table('center_cancelation_reasons')->get();
        return ['stat'=>true,'message'=>'Success','payload'=>$reasons];
    }
    
    public function createCenterCancelationReason($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'name'=>['required','string'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        if(DB::table('center_cancelation_reasons')->where('center_id',$request->center_id)->count() == 0){
            DB::table('center_cancelation_reasons')->insert([
                    [
                        'center_id'=>$request->center_id,
                        'name'=>'Duplicate Appointment',
                    ],
                    [
                        'center_id'=>$request->center_id,
                        'name'=>'Not Available',
                    ]
                ]);
        }
        $reason = DB::table('center_cancelation_reasons')->insert([
                        'center_id'=>$request->center_id,
                        'name'=>$request->name,
                    ]);
        return ['stat'=>true,'message'=>'Success','payload'=>'reason created'];
    }
    
    public function deleteCenterCancelationReason($request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'reason_id'=>['required','exists:center_cancelation_reasons,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $reason = DB::table('center_cancelation_reasons')->where('id',$request->reason_id);
        if(!$reason->first() || $reason->first()->center_id != $request->center_id){
            return ['stat'=>false,'message'=>'Error','payload'=>'center not found'];
        }
        $reason->delete();
        return ['stat'=>true,'message'=>'Success','payload'=>'reason deleted'];
    }
    
    public function getCenterReminders($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
                'reminder_id'=>['sometimes','required','exists:center_reminders,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        
        $query = DB::table('center_reminders');
        if($request->filled('reminder_id') && !is_null($request->filled('reminder_id'))){
            $query->where('id',$request->reminder_id);
        }
        $reminders = $query->get();
        return ['stat'=>true,'message'=>'Success','payload'=>$reminders];
    }
    
    public function createCenterReminder($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
                'timeframe'=>['required','numeric','min:1'],
                'via_email'=>['required','boolean',],
                'via_notification'=>['required','boolean',],
                'note'=>['nullable','string','max:500'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $reminder = DB::table('center_reminders')->insert([
                'center_id'=>$request->center_id,
                'timeframe'=>$request->timeframe,
                'via_email'=>$request->via_email,
                'via_notification'=>$request->via_notification,
                'note'=>$request->note,
                'status'=>1,
            ]);
        return ['stat'=>true,'message'=>'Success','payload'=>$reminder];
    }
    
    public function updateCenterReminder($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
                'reminder_id'=>['required','exists:center_reminders,id'],
                'timeframe'=>['required','numeric','min:1'],
                'via_email'=>['required','boolean',],
                'via_notification'=>['required','boolean',],
                'note'=>['nullable','string','max:500'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $reminder = DB::table('center_reminders')->where('id',$request->reminder_id);
        if(!$reminder->first() || $reminder->first()->center_id != $request->center_id){
            return ['stat'=>false,'message'=>'Error','payload'=>'reminder not found'];
        }
        $reminder->update([
                'timeframe'=>$request->timeframe,
                'via_email'=>$request->via_email,
                'via_notification'=>$request->via_notification,
                'note'=>$request->note,
            ]);
        return ['stat'=>true,'message'=>'Success','payload'=>'reminder updated'];
    }
    
    public function toggleCenterReminder($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
                'reminder_id'=>['required','exists:center_reminders,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $reminder = DB::table('center_reminders')->where('id',$request->reminder_id);
        if(!$reminder->first() || $reminder->first()->center_id != $request->center_id){
            return ['stat'=>false,'message'=>'Error','payload'=>'reminder not found'];
        }
        $reminder->update([
                'status'=>!$reminder->first()->status,
            ]);
        return ['stat'=>true,'message'=>'Success','payload'=>'reminder toggled'];
    }
    
    public function deleteCenterReminder($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
                'reminder_id'=>['required','exists:center_reminders,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$validator->errors()->first(),'payload'=>$validator->errors()];
        }
        $reminder = DB::table('center_reminders')->where('id',$request->reminder_id);
        if(!$reminder->first() || $reminder->first()->center_id != $request->center_id){
            return ['stat'=>false,'message'=>'Error','payload'=>'reminder not found'];
        }
        $reminder->delete();
        return ['stat'=>true,'message'=>'Success','payload'=>'reminders'];
    }
    
    public function getCenterSettings($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$request->errors()->first(),'payload'=>$request->errors()];
        }
        $center = Center::findOrFail($request->center_id);
        $settings = DB::table('center_settings')
            ->where('center_id',$center->id)
            ->when($request->filled('setting_key'),function($query)use($request){
                $query->where('key',$request->setting_key);
            })->get();
        
        return ['stat'=>true,'message'=>'Success','payload'=>$settings];
    }
    
    public function setCenterSettings($request)
    {
        $validator = Validator::make($request->all(),[
                'center_id'=>['required','exists:centers,id'],
                'settings'=>['required','array','min:1'],
            ]);
        if($validator->fails()){
            return ['stat'=>false,'message'=>$request->errors()->first(),'payload'=>$request->errors()];
        }
        $center = Center::findOrFail($request->center_id);
        foreach($request->settings as $key=>$value){
            $query = DB::table('center_settings')->where( [['center_id',$center->id],['key',$key]] );
            if($query->first()){
                $query->update(['value'=>$value]);
            }else{
                $query->insert([
                    'center_id'=>$center->id,
                    'key'=>$key,
                    'value'=>$value,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }
        }
        return ['stat'=>true,'message'=>'Success','payload'=>'settings'];
    }
    
    public function getCenterNotifications($request)
    {
        $admin = Admin::where([
            ['center_id',$request->center_id],
            ['center_position','owner'],
            ['id',auth()->user()->id]
        ])->first();
        if(!$request->center_id || auth()->user()->center_id != $request->center_id){
            return ['stat'=>false,'message'=>'error','payload'=>'No notification found'];
        }
        return ['stat'=>true,'message'=>'success','payload'=>auth()->user()->Notifications()->where('status',0)->get()];
    }
    
    public function setCenterNotificationSeen($request)
    {
        $notifications = Notification::when($request->filled('notification_id'),function($query)use($request){
            $query->where('id',$request->notification_id);
        })
        ->when(is_array($request->notifications),function($query) use ($request){
            $query->whereIn('id',$request->notifications);
        })->update(['status'=>1]);
        return ['stat'=>true,'message'=>'success','payload'=>'Notifications seen'];
    }
    
    // XXXXXX Validation XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

    public function validateCenterDetails($data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'unique:centers,email'],
            'phone_number' => ['required', 'regex:/^[+][1-9]{1,4}[-][1-9]{6,9}$/'],
            'longitude' => [
                'prohibits:country,city', 'nullable', 'numeric', 'between:-180,180',
                'required_with:latitude', 'required_without_all:country,city',
            ],
            'latitude' => [
                'prohibits:country,city', 'nullable', 'numeric', 'between:-90,90',
                'required_with:longitude', 'required_without_all:country,city',
            ],

            'about' => ['required', 'string', 'max:2000',],
            'logo' => ['sometimes','required','file', 'image', 'between:2,512'],
            'images'=>['sometimes','required','array','between:1,3',],
            'images.*'=>['required','file', 'image', 'between:2,512',],
            'center_id' => ['sometimes','required','exists:centers,id'],
        ]);
    }

    public function validateCenterDetailsUpdate($data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email:rfc,dns',],
            'phone_number' => ['required', 'regex:/^[+][1-9]{1,4}[-][1-9]{6,9}$/'],

            'longitude' => [
                'prohibits:country,city', 'nullable', 'numeric', 'between:-180,180',
                'required_with:latitude', 'required_without_all:country,city',
            ],
            'latitude' => [
                'prohibits:country,city', 'nullable', 'numeric', 'between:-90,90',
                'required_with:longitude', 'required_without_all:country,city',
            ],

            'about' => ['required', 'string', 'max:2000',],
            'main_branch' => ['nullable', 'string',],
            'logo' => ['nullable','file', 'image', 'between:2,512'],
            'images'=>['nullable','array','between:1,3',],
            'images.*'=>['nullable','file', 'image', 'between:2,512',],
        ]);
    }

    public function validateCenterDays($data)
    {
        return Validator::make($data, [
            'center_id' => ['required','exists:centers,id'],
            'days' => ['required','array','min:1'],
            'days.*.open' => ['date_format:H:i', 'required_with:days.*.close',],
            'days.*.close' => ['date_format:H:i', 'required_with:days.*.open',],
        ]);
    }

    public function validateCenterContact($data)
    {
        return Validator::make($data, [
            'center_id' => ['required','exists:centers,id'],
        ]);
    }

    public function validateCenterCategories($data)
    {
        return Validator::make($data,[
            'center_id'=>['required','exists:centers,id'],
            'category'=>['required','array'],
            'category.*'=>['string','exists:categories,name'],
        ]);
    }
    
    public function validateCenterServices($data)
    {
        return Validator::make($data,[
            'center_id' => ['required','exists:centers,id'],
            'center_category_id' => ['required','exists:center_categories,id'],
            'name' => ['required','string'],
            'Treatment_type' => ['nullable','string','max:100',],
            'description' => ['required','string','max:1000',],	
            'Aftercare_description'	=> ['nullable','string','max:1000',],
            'service_gender' => ['required','in:everyone,females,males',],
            'Duration' => ['required','numeric','between:15,600'],
            'price_type' => ['required','in:fixed,free',],
            'retail_price' => ['prohibited_if:price_type,free','exclude_if:price_type,free','numeric','min:10.00','max:1000.00'],
        ]);
    }
    
    public function validateMemberDetails($data)
    {
        return Validator::make($data,[
            'first_name'=>['required','string',],
            'last_name'=>['required','string',],
            'email'=>['required','email','unique:admins',],
            'password'=>['required','confirmed','min:6',],
            'center_id'=>['required','exists:centers,id',],
            'center_position'=>['required','string',],
            'days'=>['required','array','max:6'],
            'days.*'=>['exists:open_days,id'],
            'services'=>['required','array','min:1'],
            'services.*'=>['exists:center_services,id',]
            ]);
    }
    
    public function validateAdminDetails($data)
    {
        return Validator::make($data,[
            'center_id' => ['required','exists:centers,id'],
            'admin_first_name'=>['required','string',],
            'admin_last_name'=>['required','string',],
            'admin_email'=>['sometimes','required','email:rfc,dns','unique:admins,email',],
            'admin_password'=>['sometimes','required','confirmed','min:6',],
            'member_desc'=>['required','string','max:300']
            ]);
    }
    
    public function validateOfferDetails($data)
    {
        return Validator::make($data,[
                'name'=>['required','string','max:40'],
                'description'=>['required','string','max:300'],
                'center_id'=>['required','exists:centers,id'],
                'start_date'=>['required','date_format:Y-m-d'],
                'end_date'=>['required','date_format:Y-m-d'],
                'value'=>['required','numeric','between:1,100'],
                'services'=>['required','array','min:1'],
                'services.*'=>['required','exists:center_services,id'],
            ]);
    }
    

    public function checkCenterExists($name, $email)
    {
        return Center::where([['name', $name], ['email', $email]])->first();
    }
}
