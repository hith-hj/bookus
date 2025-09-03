<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index(Request $request){
        $settings = Setting::first();
        return view('admin.pages.settings',['settings'=>$settings]);
    }
    
    public function update(Request $request){
    	$request->validate([
		    "name" => ['required','string',],
		    "email" => ['required','string',],
		    "location" => ['required','string',],
		    "phone" => ['required','string'] ,
		    "landline" => ['required','string'],
		    "whatsapp" => ['required','string',],
		    "duration" => ['required','numeric','between:30,60'],
		    "max_price" => ['required','numeric','max:10000'],
		    // "open" => ['required',],
		    // "close" => ['required',],
		    "tax" => ['required','numeric','between:1,10'],
		    "currency" => ['required','string'],
    		"version" => ["required",'numeric'],
    	]);
    	$settings = Setting::first()->update($request->all());
    	return redirect()->back()->with('success','Settings Updated');
    }
    
}