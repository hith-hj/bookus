<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use App\Models\Center;
use App\Models\Admin;
use App\Models\Image;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



class CenterController extends Controller
{
    private $settingsToStore=[
            'create_appointments'=>true,
            'cancel_appointments'=>true,
            'reschedule_appointments'=>true,
            'cancelation_range'=>6,
            'member_selection'=>true,
        ];

    public function index(Request $request ,$perPage = 5){
        $centers = Center::when($request->filled('search'),function($query) use ($request){
            $query->where([['name','like','%'.$request->search.'%']]);
        })->when($request->filled('filter') && in_array($request->filter,$this->filters()),
            function($query) use ($request){
                $query = $this->setFilters($request,$query);
        })->when( $request->filled('perPage'), function() {
            $perPage = $request->perPage;
        })->paginate($perPage);
        $admins = $this->getAdmins();
        return view('admin.pages.centers',['centers'=>$centers,'admins'=>$admins]);
    }

    private function getAdmins(){
        return Admin::where([['center_id',null],['center_position',null]])->get(['id','first_name']);
    }

    private function setFilters($request, $query){
        switch ($request->filter) {
            case 'highest':
                $query->where('rated','>=','6');
                break;
            case 'lowest':
                $query->where('rated','<=','3');
                break;
            case 'verified':
                $query->where('status',1);
                break;
            case 'unverified':
               $query->where('status',0);
                break;                    
            default:
                return back()->with('error','Filter not valid');
                break;
        } 
        return $query;       
    }

    private function filters(){
        return ['rated'=>'highest','rated'=>'lowest','status'=>'verified','status'=>'unverified'];
    }
    
    public function center(Request $request , $id){
        $center = Center::with(['resources','cGifts','cMemberships','appointments'])->findOrFail($id);
        $center->isOpen = $this->centerIsOpen($center);
        $admins = $this->getAdmins();
        return view('admin.pages.center',['center'=>$center,'admins'=>$admins]);
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

    public function block(Request $request , $id){
        $center = Center::findOrFail($id);
        if($center->status == 1 ){
            $center->update(['status'=>-1]);
            return redirect()->back()->with('success','Center is Blocked');
        }
        return redirect()->back()->with('error','Something went wrong');
    }

    public function unblock(Request $request , $id){
        $center = Center::findOrFail($id);
        if($center->status == -1 ){
            $center->update(['status'=>1]);
            return redirect()->back()->with('success','Center is Un blocked');
        }
        return redirect()->back()->with('error','Something went wrong');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>['required','string','max:50'],
            'email'=>['required','email','unique:centers,email'],
            'phone_number'=>['required','regex:/^[+][1-9]{1,4}[0-9]{6,9}$/',],
            'admin'=>['required','exists:admins,id'],
            'about'=>['nullable','string','max:500'],
            'logo'=>['nullable','image'],
            'images'=>['nullable','array','max:5'],
            'images.*'=>['image']
        ]);
        $admin = Admin::findOrFail($request->admin);

        $center = new Center(populateModelData($request, Center::class));
        $center->status = 0;
        $center->save();
        $admin->update(['is_admin'=>1,'center_id'=>$center->id,'center_position'=>'owner']);
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
        return redirect()->route('admin.centers')->with('success','Center is created');
    }

    public function edit(Request $request, $id){
        $request->validate([
            'name'=>['required','string','max:50'],
            'email'=>['required','email',],
            'phone_number'=>['required','regex:/^[+][1-9]{1,4}[0-9]{6,9}$/',],
            'admin'=>['required','exists:admins,id'],
            'about'=>['nullable','string','max:500'],
            'logo'=>['nullable','image'],
            'images'=>['nullable','array','max:5'],
            'images.*'=>['image']
        ]);
        $center = Center::findOrFail($id);
        $admin = $center->admin();
        if($admin->id != $request->admin){
            $admin->update(['is_admin'=>0,'center_id'=>null,'center_position'=>null]);
            Admin::find($request->admin)->update([
            'is_admin'=>1,
            'center_id'=>$center->id,
            'center_position'=>'owner',
        ]);
        }

        if($center->email != $request->email && Center::where('email',$request->email)->count() > 0){
            return back()->with('error','Email already taken');
        }

        $center->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone_number'=>$request->phone_number,
            'about'=>$request->about,
        ]);

        $images_obj = $center->images;
        if ($request->hasFile('logo')) {
            if ($images_obj->logo != null) {
                $images_obj->logo = Storage::disk('public')->delete($images_obj->logo);
            }
            $images_obj->logo = Storage::disk('public')->put('center_logo', $request->file('logo'));
            $images_obj->save();
        }
        $images = $center->images->images;
        if ($request->hasFile('images')) {
            if(isset($images)){
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
                $images_obj->images = null;
            }
            $photos = uploadMultiImages("images", "centers");
            $images_obj->images = $photos;
            $images_obj->save();
        }

        return redirect()->route('admin.center',$center->id)->with('success','center updated');
    }

    public function delete($id){
        $center = Center::findOrFail($id);
        $center->days()->delete();
        $center->contacts()->delete();
        $center->categories()->delete();
        $center->centerServices()->delete();
        $center->reviews()->delete();
        $center->appointments()->delete();
        $center->offers()->delete();
        $center->gifts()->delete();
        $center->cGifts()->delete();
        $center->memberships()->delete();
        $center->cMemberships()->delete();
        $center->resources()->delete();
        $center->admins()->delete();
        $images_obj = $center->images;
        if ($images_obj->logo != null) {
            $images_obj->logo = Storage::disk('public')->delete($images_obj->logo);
        }
        $images = $center->images->images;
        if(isset($images)){
            foreach ($images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        $center->images()->delete();

        DB::table('center_settings')->where('center_id',$center->id)->delete();
        $center->delete();
        Center::where('main_branch',$id)->update(['main_branch'=>null]);
        return redirect()->route('admin.centers')->with('success','center deleted');
    }
}
