<?php 

namespace App\Http\Controllers\User;

use App\Models\Admin;
use App\Models\User;
use App\Models\Center;
use App\Models\Favorite;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CenterController extends Controller
{

    public function center($id = null){
        $center = Center::with(['resources','cGifts','cMemberships'])->findOrFail($id);
        $center->isOpen = $this->centerIsOpen($center);
        $center->isFavorite = $this->centerIsFavorite($center);
        return view('user.pages.center',['center'=>$center]);
    }
    
    public function centerIsOpen($center){
        $days = $center->days->keyBy('day')->toArray();
        $today = Carbon::now()->format("l");
        $tz = "+3";
        $time = Carbon::now($tz);
        $isOpen = false;
        if( array_key_exists($today,$days) ){
            $open = Carbon::create($days[$today]['open'],$tz);
            $close = Carbon::create($days[$today]['close'],$tz);
            if($time->gte($open) && $time->lte($close)){
                $isOpen = true;
            }
        }
        return $isOpen;
    }
    
    public function centerIsFavorite($center){
        return Favorite::where([['user_id',auth()->id()],['center_id',$center->id]])->exists();
    }
    
    public function toggleFavorite(Request $request, $id){
        $center = Center::findOrFail($id);
        if(Favorite::where([['user_id',auth()->id()],['center_id',$center->id]])->exists() ){
            auth()->user()->favorites()->detach($center->id);
            return back()->with('success',"$center->name removed from your favorits");
        }else{
            auth()->user()->favorites()->attach($center->id);
            return back()->with('success',"$center->name added to your favorits");
        }
    }
    
    
}