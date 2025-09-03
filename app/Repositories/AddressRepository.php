<?php

namespace App\Repositories;

use App\Models\Address;
use App\Models\Category;
use App\Models\Center;
use App\Models\Contact;
use App\Models\Image;
use App\Models\OpenDay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class  AddressRepository
{

    public function add(Request $request,$user)//: User
    {
        $request->validate([
            'name'=>['required','string',],
            'address_title'=>['required','string',],
            'district'=>['required','string',],
            'apartment'=>['required','string','max:5'],
            'postcode'=>['required','numeric',],
            'longitude' => ['required', 'numeric', 'between:-180,180', 'required_with:latitude',],
            'latitude' => ['required', 'numeric', 'between:-90,90', 'required_with:longitude',],
            'default'=>['required','boolean'],
        ],['name.required'=>'Address name is required']);
        $allAddresses = Address::where('user_id',$user->id)->get();
        $address = new Address(populateModelData($request, Address::class));
        if($request->get('default')==1){
            $is_default = 1;
            foreach($allAddresses as $adrs){
                $adrs->default = 0;
                $adrs->save();
            }
        }else{
            $is_default = 0;   
        }
        $address->default = $is_default;
        $address->user_id = $user->id;
        $address->save();
    }
    

    public  function getAddresses( Request $request,$user)
    {
    $addresses=Address::query()->where('user_id',$user->id);
    if ($search = $request->get('search')) {
        $addresses->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');

        });}
        return $addresses->latest();
    }
    
    
    public function getAddreses( $address) 
    {
        $addressDetails=Category::query()->findOrFail($address);
        return $addressDetails;
    }
    
    
    public function update(Request $request,$address)  
    {
        $request->validate([
            'name'=>['required','string',],
            'address_title'=>['required','string',],
            'district'=>['required','string',],
            'apartment'=>['required','string','max:5'],
            'postcode'=>['required','numeric',],
            'longitude' => ['required', 'numeric', 'between:-180,180', 'required_with:latitude',],
            'latitude' => ['required', 'numeric', 'between:-90,90', 'required_with:longitude',],
            'default'=>['required','boolean'],
        ],['name.required'=>'Address name is required']);
        if($request->get('default') == 1){
            $addresses = Address::where('user_id',auth('client')->id())->get();
            foreach($addresses as $adrs){
                $adrs->default = 0;
                $adrs->save();
            }
            $is_default = 1;
        }else{
            $is_default = 0;
        }
        $address->update(populateModelData($request, Address::class));
        $address->default = $is_default;
        $address->save();
    }
    

    public function delete(Address $address)
    {
        if($address->default == 1){
            $defAddress = Address::where('user_id',auth('client')->id())->orderBy('created_at','ASC')->first();
            $defAddress->default = 1;
            $defAddress->save();
        }
        $address->delete();
    }
    
    
   public function getAllAddresses()
   {
        return Address::query()->get();
   }

}
