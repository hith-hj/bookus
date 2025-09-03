<?php

namespace App\Http\Controllers\Center\Repos;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Image;
use App\Models\User;
use App\Models\Note;
use App\Models\Center;
use App\Models\Appointment;
use App\Models\AppointmentServices;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Models\Contact;
use App\Models\OpenDay;
use App\Models\MemberShip;
use App\Models\Gift;
use App\Models\Review;
use Carbon\Carbon;


class  CenterClientsRepository
{
    public function checkUserExists($request)
    {
        if(!$request->filled('email')){
            return ['stat'=>false,'message'=>'Error','payload'=>"Email is required"];
        }
        $request->validate(['email'=>'required|email:dns,rfc']);
        $user = User::where('email',$request->email)->first(['id','first_name','last_name','email']);
        if(!$user){
            return ['stat'=>false,'message'=>'Error','payload'=>"User Not Found"];
        }
        return ['stat'=>true,'message'=>'success','payload'=>$user];
    }
    
    public function centerClientsQuery($request)
    {
        $center_id =$request->center_id ?? auth()->user()->center_id;
        $center = Center::find($center_id);
        $appointments = Appointment::where('center_id',$center_id)->pluck('user_id');
        $memberships = Membership::where('center_id',$center_id)->pluck('user_id');
        $gifts = Gift::where('center_id',$center_id)->pluck('user_id');
        $users = User::where('code','LIKE','%'.Str::camel($center->name).'%')->pluck('id');
        $ids = collect([$users,$appointments,$memberships,$gifts])->flatten()->unique();
        $query = User::query()->whereIn('id',$ids);
        if($request->has('filters') ){
            foreach($request->filters as $key => $value){
                if (Schema::hasColumn('users', $key)){
                     $query->where($key,$value);   
                }else{
                    $error["FILTERBY_$key"] = "Is Not Filterable Column";
                }
            }
        }
        if($request->has('orderBy')){
            foreach($request->orderBy as $key => $value){
                if (Schema::hasColumn('users', $key) && in_array($value ,['desc','asc'])){
                    $query->orderBy($key,$value);  
                }else{
                    $error["ORDERBY_$key"] = "$value Is Not Valid For Order";
                }
            }
        }
        return isset($error) ? $error : $query->get(['id','first_name','last_name','email','status','code']);
    }
    
    public function centerClientDetails($request)
    {
        $center_id = $request->center_id ?? auth()->user()->center_id;
        $client = User::where('id',$request->client_id)->first(
            ['id','first_name','last_name','email','phone_number','birth_date','gender','created_at','code']
            );
        if(!$client){
            return ['stat'=>false,'message'=>'Error','payload'=>"Client Not Found"];
        }
        if($request->has('statistics')){
            $condition = [
                    ['center_id',$center_id],
                    ['user_id',$request->client_id]
                    ];
            $client->stats = [
                'reviews'=>Review::where($condition)->get(),
                'visits'=> Appointment::where($condition)->with('AppointmentServices')->get(),
                'notes'=>Note::where([['center_id',$center_id],['user_id',$client->id]])->get(),  
                'sales'=>[
                    'gifts'=>Gift::where($condition)->get(),
                    'memberships'=>$client->memberships()->with('services')->get(),
                    ],
                ];
        }
        return ['stat'=>true,'message'=>'success','payload'=>$client];
    }
    
    public function addCenterClient($request)
    {
        $center = Center::find($request->center_id);
        if(!$center){
            return ['stat'=>false,'message'=>'Center Error','payload'=>'Not Found'];
        }
        $user = new User($request->except(['password']));
        $user->code = Str::camel($center->name).'_'.Str::random(6);
        $user->password = bcrypt($request->password);
        $user->save();
        $token = $user->createToken('API');
        $user->token = $token->plainTextToken;
        $user->is_verified =1;
        $user->status =1;
        $user->save();
        return ['stat'=>true,'message'=>'Client Created','payload'=>$user];
    }
    
    public function updateCenterClient($request)
    {
        $center = Center::find($request->center_id);
        $user = User::where([['id',$request->user_id],['code',$request->code]])->first();
        if(!$center || !$user || $user->status == -1){
            return ['stat'=>false,'message'=>'Error','payload'=>'Center Or User Not Found'];
        }
        if(!str_contains($user->code,Str::camel($center->name))){
            return ['stat'=>false,'message'=>'Center Error','payload'=>'user is not your client, you can not delete this user'];
        }
        $user->update($request->all());
        return ['stat'=>true,'message'=>'success','payload'=>$user];
    }
    
    public function deleteCenterClient($request)
    {
        $center = Center::find($request->center_id);
        if(!$center){
            return ['stat'=>false,'message'=>'Center Error','payload'=>'Center Not Found'];
        }
        $user = User::where([['id',$request->user_id],['code',$request->code]])->first();
        if(!$user || $user->status == -1){
            return ['stat'=>false,'message'=>'User Error','payload'=>'User Not Found'];
        }
        if(!str_contains($user->code,Str::camel($center->name))){
            return ['stat'=>false,'message'=>'Center Error','payload'=>'user is not your client, you can not delete this user'];
        }
        $user->code ='';
        $user->save();
        return ['stat'=>true,'message'=>'Success','payload'=>'Deleted'];
    }
    
    public function addCenterClientNote($request)
    {
        $center_id = $request->center_id ?? auth()->user()->center_id;
        $center = Center::find($center_id);
        if(!$center){
            return ['stat'=>false,'message'=>'Center Error','payload'=>'Center Not Found'];
        }
        // $user = User::find($request->user_id);
        // if($user->code == null || !str_contains($user->code,Str::camel($center->name))){
        //     return ['stat'=>false,'message'=>'Center Error','payload'=>'user is not your client, you can\'t note him'];
        // }
        $note = Note::updateOrCreate(['center_id'=>$request->center_id,'user_id'=>$request->user_id],$request->all());
        return ['stat'=>true,'message'=>'note created','payload'=>$note];
    }
    
    public function getCenterNote($reuest)
    {
        $center_id = $request->center_id ?? auth()->user()->center_id;
        $center = Center::find($center_id);
        if(!$center){
            return ['stat'=>false,'message'=>'Center Error','payload'=>'Center Not Found'];
        }
        $notes = Note::where('center_id',$center->id)->get();
        return ['stat'=>true,'message'=>'Note List','payload'=>$notes];
    }
    
    public function getCenterMemberships($request)
    {
        $center_id =$request->center_id ?? auth()->user()->center_id;
        $center = Center::findOrFail($center_id);
        $query = Membership::query()->with([
            'services',
            'users:id,first_name,last_name,email',
            ])->where('center_id',$center_id)
            ->when($request->has('membership_id') && $request->filled('membership_id'),function($query) use($request){
                $query->where('id',$request->membership_id);
            });
        if($request->has('filters') && !$request->has('membership_id') ){
            foreach($request->filters as $key => $value){
                if (!Schema::hasColumn('memberships', $key)){
                    $Error["FILTERBY_$key"] ='Is not filterable column ';
                    break;
                }
                 $query->where($key,$value);   
            }
        }
        if($request->has('orderBy') && !$request->has('membership_id')){
            foreach($request->orderBy as $key => $value){
                if (!Schema::hasColumn('memberships', $key)){
                    $Error["ORDERBY_$key"] = 'Is not Orderable Column';
                    break;
                }
                if(!in_array($value,['desc','asc'])){
                    $Error["ORDERBY_$key.$value"] = "$value Is not Valid value for Order";
                    break;
                }
                $query->orderBy($key,$value);
            }
        }
        return isset($Error) ? $Error : $query->get();
    }
    
    public function addCenterMembership($request)
    {
        $center = Center::findOrFail($request->center_id);
        $request['remaining'] = $request->session;
        $request['statue'] = 'valid';
        $duration = explode('-',$request->duration);
        if( empty($duration[0]) ||  empty($duration[1]) ){
            return ['stat'=>false,'message'=>'Duration is required','payload'=>''];
        }
        switch($duration[1]){
            case 'day':
                    if($duration[0] > 365){
                        return ['stat'=>false,'message'=>'Days can\'t be greater than 365','payload'=>''];
                    }
                break;
            case 'month':
                if($duration[0] > 12){
                        return ['stat'=>false,'message'=>'Moths can\'t be greater than 12','payload'=>''];
                    }
                break;
            case 'year':
                if($duration[0] > 1){
                        return ['stat'=>false,'message'=>'Days can\'t be greater than 1','payload'=>''];
                    }
                break;
            default:
                    return ['stat'=>false,'message'=>'Duration value is not valid','payload'=>''];
                break;
        }
        $request['end_date'] = Carbon::parse($request->start_date)->add($request->duration);
        $membership = MemberShip::create($request->all());
        foreach($request->services as $id){
            $service = CenterService::findOrFail($id);
            if($service->center_id == $center->id){
                $membership_service = DB::table('membership_services')
                    ->updateOrInsert([
                            'center_services_id'=>$service->id,
                            'membership_id'=>$membership->id,
                        ],[
                            'created_at'=>now(),
                            'updated_at'=>now(),
                        ]);
            }
        }
        
        return ['stat'=>true,'message'=>'success','payload'=>$membership];
    }
    
    public function cancelCenterMembership($request)
    {
        $validate = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'membership_id'=>['required','exists:memberships,id'],
            ]);
        if ($validate->fails()){
            return ['stat'=>false,'message'=>$validate->errors()->first(),'payload'=>$validate->errors()];
        }    
        $center = Center::find($request->center_id);
        $membership = MemberShip::find($request->membership_id);
        if($membership->status != 'valid' || $membership->center_id != $center->id){
            return ['stat'=>false,'message'=>'Center Error','payload'=>'You Can Not Cancel Membership'];
        }
        if($membership->users()->wherePivot('status','valid')->count() > 0){
            return ['stat'=>false,'message'=>'Center Error','payload'=>'You Can Not Cancel Membership while there is a valid supscribtion'];
        }
        $membership->status = 'canceled';
        $membership->save();
        return  ['stat'=>true,'message'=>'Success','payload'=>'Membership canceled'];
    }
    
    public function addClientMembership($request)
    {
        $center = Center::findOrFail($request->center_id);
        $membership = MemberShip::findOrFail($request->membership_id);
        if($center->id != $membership->center_id){
            return ['stat'=>false,'message'=>'Error','payload'=>'Unauthonrized'];
        }
        if($membership->status != 'valid'){
            return ['stat'=>false,'message'=>'Error','payload'=>'the membership is unvalid'];
        }
        $user = User::findOrFail($request->user_id);
        if($user->memberships()->wherePivot('status','valid')->wherePivot('membership_id',$membership->id)->exists() == false){
            $membership_user = $user->memberships()->attach($membership->id,[
                'remaining'=>$membership->session,
                'status'=>'valid',
                ]);   
        }else{
            return ['stat'=>true,'message'=>'success','payload'=>'You already Have a valid membership'];
        }
        return ['stat'=>true,'message'=>'success','payload'=>'Membership created successfuly'];
    }
    
    public function cancelClientMembership($request)
    {
        $validate = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id'],
            'membership_id'=>['required','exists:memberships,id'],
            // 'user_id'=>['required','exists:users,id'],
            ]);
        if ($validate->fails()){
            return ['stat'=>false,'message'=>$validate->errors()->first(),'payload'=>$validate->errors()];
        }    
        $center = Center::find($request->center_id);
        $membership = MemberShip::find($request->membership_id);
        if($membership->status != 'valid' || $membership->center_id != $center->id){
            return ['stat'=>false,'message'=>'Center Error','payload'=>'You Can Not Cancel Membership'];
        }
        $membership->status = 'canceled';
        $membership->save();
        return  ['stat'=>true,'message'=>'Success','payload'=>$membership];
    }
    
    
    public  function getCenterGifts($request)
    {
        $center_id =$request->center_id ?? auth()->user()->center_id;
        $center = Center::findOrFail($center_id);
        $query = Gift::query()
        ->with(['user:id,first_name,last_name,email',])
        ->where('center_id',$center_id)
        ->when($request->filled('gift_id'),function($query) use ($request){
            $query->where('id',$request->gift_id);
        });
        if($request->has('filters') && !$request->has('gift_id') ){
            foreach($request->filters as $key => $value){
                if (!Schema::hasColumn('gifts', $key)){
                    $Error["FILTERBY_$key"] ='Is not filterable column ';
                    break;
                }
                $query->where($key,$value);   
            }
        }
        if($request->has('orderBy') && !$request->has('gift_id')){
            foreach($request->orderBy as $key => $value){
                if (!Schema::hasColumn('gifts', $key)){
                    $Error["ORDERBY_$key"] = 'Is not Orderable Column';
                    break;
                }
                if(!in_array($value,['desc','asc'])){
                    $Error["ORDERBY_$key.$value"] = "$value Is not Valid value for Order";
                    break;
                }
                $query->orderBy($key,$value);
            }
        }
        return isset($Error) ? $Error : $query->get();
    }
    
    public  function addClientGift($request)
    {
        $request['end_date'] = Carbon::parse($request->start_date)->add($request->duration);
        $request['remaining'] = $request->value;
        $request['status'] = 'valid'; 
        
        $gift = Gift::updateOrCreate(['user_id'=>$request->user_id,'center_id'=>$request->center_id,'status'=>'valid'],$request->all());
        return ['stat'=>true,'message'=>'Gift Created','payload'=>$gift];
    }
    
    public  function cancelClientGift($request)
    {
        $request->validate(['center_id'=>['required','exists:centers,id'],'gift_id'=>['required','exists:gifts,id']]);
        $center = Center::findOrFail($request->center_id);
        $gift = Gift::findOrFail($request->gift_id);
        if($gift->center_id != $center->id ){
            return ['stat'=>false,'message'=>'Error','payload'=>'Unauthorized Action'];
        }
        if($gift->status != 'valid'){
            return ['stat'=>false,'message'=>'Error','payload'=>'Gift Can Not be Canceled more than once'];
        }
        $gift->status = 'canceled';
        $gift->save();
        return ['stat'=>true,'message'=>'Gift Canceled','payload'=>$gift];
    }
    
    
    public function validateGiftDetails($data)
    {
        return Validator::make($data,[
            'center_id' =>['required','exists:centers,id'],
            'user_id' =>['required','exists:users,id'],
            'start_date' =>['required','date_format:Y-m-d'],
            'duration' =>['required','string'],
            'value' =>['required','numeric','between:0,20000'],
            ]);
    }
    
    public function validateMembershipDetails($data)
    {
        return Validator::make($data,[
            'center_id'=>['required','exists:centers,id'],
            'name'=>['required','string'],
            'start_date'=>['required','date_format:Y-m-d'],
            'session'=>['required','numeric'],
            'duration'=>['required','string'],
            'terms'=>['required','string','max:1000'],
            'description'=>['required','string','max:300'],
            'services'=>['required','array','min:1'],
            'services.*'=>['exists:center_services,id'],
            'total'=>['required','numeric','between:100,100000'],
            ]);
    }
    
    public function validateCenterClientDetails($data)
    {
        return Validator::make($data,
            [
                'center_id'=>['required','exists:centers,id'],
                'first_name'=>['required','string','max:50',],
                'last_name'=>['required','string','max:50',],
                'phone_number'=>['required','regex:/^[+]{1}[1-9]{1}[0-9]{0,2}[-][1-9]{6,9}$/',],
                'password'=>['required','min:6','confirmed'],
                'email'=>['required','email',"unique:users"],
                'gender'=>['required','in:male,female',],
                'description'=>['nullable','string','max:300'],
                'birth_date'=>['required','date_format:Y-m-d'],
            ]
        );
    }
    
    public function validateCenterClientUpdteDetails($data)
    {
        return Validator::make($data,
            [
                'center_id'=>['required','exists:centers,id'],
                'user_id'=>['required','exists:users,id'],
                'first_name'=>['required','string','max:50',],
                'last_name'=>['required','string','max:50',],
                'code'=>['required','exists:users',],
                'phone_number'=>['required','regex:/^[+]{1}[1-9]{1}[0-9]{0,2}[-][1-9]{9}$/',],
                'email'=>['required','email',],
                'gender'=>['required','in:male,female',],
                'birth_date'=>['required','date_format:Y-m-d'],
            ]
        );
    }
    
    public function validateMembershipClient($data)
    {
        return Validator::make($data,
            [
                'center_id'=>['required','exists:centers,id'],
                'user_id'=>['required','exists:users,id'],
                'membership_id'=>['required','exists:memberships,id'],
            ]
        );
    }
}