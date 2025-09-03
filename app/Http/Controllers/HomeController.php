<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Center;
use App\Models\CenterService;

class HomeController extends Controller
{
    public function index(){
        $featured = $this->featured();
        $offers = $this->offers();
        return view('user.index',['featured'=>$featured,'offers'=>$offers]);
    }
    
    public function about(){
        return view('user.about');
    }
    
    public function featured(){
        return Center::where('rated','>=',4)->get(['id','name','rated',])->sortByDesc('rated')->take(3);
    }

    public function offers(){
        return Center::withWhereHas("offers")->get(['id','name','rated',])->take(6);
    }
    
    public function search(){
        $query = request()->q;
        $centers = Center::where('name','LIKE','%'.$query.'%')->get(['id','name']);
        $services = CenterService::where('name','LIKE','%'.$query.'%')->get(['id','name','center_id']); 
        return json_encode(['centers'=>$centers,'services'=>$services]);
    }
     
    public function center(){
        $id = request()->id;
        $center = Center::where('id',$id)->firstOrFail();
        if(is_null($center)){
            return json_encode(['error_desc'=>'center not found']);
        }
        return view('user.pages.center',['center'=>$center]);
    }
}



















