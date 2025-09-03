<?php

namespace App\Http\Controllers\AppUser;
use App\Http\Controllers\Admin\Application;
use App\Http\Controllers\Admin\Factory;
use App\Http\Controllers\Admin\View;
use App\Models\Appointment;
use App\Models\Center;
use App\Models\CenterService;
use App\Models\ContactUser;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\User;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
class HomeController extends ApiController
{

    public function recommended()
    {
        $centers=Center::query()->whereBetween('rated',[2,4])->get($this->Attrs())->take(20);
        return $this->respondSuccess(['centers'=>$this->Attrs($centers)]);
    }

    public function featured(){
        $centers=Center::query()->where('rated',5)->get($this->Attrs())->take(20);
        return $this->respondSuccess(['centers'=>$this->Attrs($centers)]);
    }


    public function offers(Request $request)
    {
        $centers=Center::query()->withWhereHas("offers")->get($this->Attrs())->take(20);
        return $this->respondSuccess(['centers'=>$this->Attrs($centers)]);
    }
    
    public function Attrs($collection = null){
        return $collection == null ? ['id','name','rated',]
        : $collection->makeHidden([
            'phone_number',
            'status',
            'country',
            'city',
            'created_at',
            'latitude',
            'longitude',
            'currency',
            'address',
            'email',
            'about',
            'admins',
            'categories',
            'days',
            'contacts',
            'centerServices',
            'center_images',
            'review',
            'branches',
            'main_branch',
            'offers',
        ]);
    }
    
    public function addReview(Request $request){
        $request->validate([
            'rate'=>'numeric|min:0|max:5',
            'review'=>'required|string|max:500',
            'center_id'=>['required','exists:centers,id']
        ]);
        $center=Center::find($request->center_id);
        $user=User::find(auth('client')->id());
        $user->reviews()
        ->withTimestamps()
        ->syncWithPivotValues($center->id,['rate'=>$request->rate,'review'=>$request->review],false);
        $reviews=Review::where('center_id',$center->id)->get();
        $center->rated=$reviews->sum('rate')/$reviews->count();
        $center->save();
        return $this->respondSuccess('Thanks for your review');
    }


    public  function search(Request $request)
    {
        $limit = $request->has('limit') && $request->limit < 30 ? $request->limit : 30;
        $centers =Center::query()
            ->when($request->filled('treatment'),function($query) use ($request){
                $query->whereHas('categories', function ($query) use ($request){
                    $query->where('name', 'like', '%' . $request->treatment . '%');
                })->orWhereHas('services', function ($query) use ($request){
                    $query->where('name', 'like', '%' . $request->treatment . '%');
                });
            })
            ->when($request->filled('latitude') && $request->filled('longitude') && !$request->filled('treatment'),
            function($query) use($request){
                $query->selectRaw('*,ceil( st_distance_sphere( point(?,?), point(longitude,latitude) ) ) as distance',
                [$request->longitude,$request->latitude])
                ->having('distance','<=',$request->distance ?? 1000);
            });    
        return $this->respondSuccess(
            $centers->get()->makeHidden(['centerServices']), 
            $this->createApiPaginator($centers->paginate($limit))
        );
    }

    public function vouchers(Request $request)
    {
        $online = [];
        $gifts = [];
        $memberships = [];
        $appointments = Appointment::with('center')->where([
            ['user_id', auth('client')->user()->id],
            ['status','completed'],])
            ->with('appointmentServices')
            ->orderBy('created_at', 'desc')
            ->get();
        $appointment_services = [];
        foreach($appointments as $app){
            $app['logo'] = $app->center->logo;
            $app['center_name'] = $app->center->name;
            match($app->using_by){
                'online'=>array_push($online,$app),
                'gift_card'=>array_push($gifts,$app),
                'membership'=>array_push($memberships,$app),
            };
        }

        $appointments->makeHidden([
            'user_id',
            'center_id',
            'member_id',
            'shift_start',
            'shift_end',
            'center',
        ]);
        
        return $this->respondSuccess([
            'online'=>$online,
            'gift'=>$gifts,
            'membership'=>$memberships,
        ]);
    }

    public function contactUs(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:100',
            'email'=>'required|email:rfc,dns|max:500',
            'message'=>'required|string|max:500',
            'subject'=>'required|string|max:500'
        ]);
        $flight = ContactUser::create([
            'name' => $request->name,
            'email'=>$request->email,
            'message'=>$request->message,
            'subject'=>$request->subject,
        ]);
     return $this->respondSuccess();
    }


}
