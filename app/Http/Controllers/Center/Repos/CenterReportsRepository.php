<?php

namespace App\Http\Controllers\Center\Repos;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Closure;
use Carbon\carbon;

use App\Models\Image;
use App\Models\Appointment;
use App\Models\Center;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Models\AppointmentServices;
use App\Models\Contact;
use App\Models\OpenDay;
use App\Models\Admin;
use App\Models\Offer;
use App\Models\Gift;
use App\Models\User;
use App\Models\MemberShip;


class  CenterReportsRepository
{
    public function salesReport($request)
    {
        $center = Center::find($request->center_id);
        if(!$center)
        {
            return ['stat'=>false,'message'=>'Error','payload'=>'center is not found'];
        }
        
        $appointments = Appointment::with(['appointmentServices'])
            ->where([['center_id',$center->id],['status','completed']])
            ->when($request->filled('filter') && isset($request->filter['start_date']) && isset($request->filter['end_date']),
                function($query) use ($request){
                    if(!is_null($request->filter['start_date']) && !is_null($request->filter['end_date']))
                    {
                        $query->whereBetween('appointment_date',
                            [
                                $request->filter['start_date'],
                                $request->filter['end_date'],
                            ]);
                    }
            })
            ->get(['id',]);
        $servicesCount = 0;
        $servicesTotal = 0;
        foreach($appointments as $appointment)
        {
            foreach($appointment->appointmentServices as $service)
            {
                $servicesCount +=1;
                $servicesTotal += $service->price;
            }
        }
        
        $memberships = MemberShip::with([
            'users'=>function(Builder $query) use($request) {
                if($request->filled('filter') && isset($request->filter['start_date']) && isset($request->filter['end_date'])){
                    if(!is_null($request->filter['start_date']) && !is_null($request->filter['end_date']))
                    {
                        $query->wherePivotBetween('created_at',
                            [
                                $request->filter['start_date'],
                                $request->filter['end_date'],
                            ]);
                    }
                }
            }
            ])
            ->where('center_id',$center->id)
            ->get();
        $membershipCount = 0;
        $membershipTotal = 0;
        foreach($memberships as $membership)
        {
            foreach($membership->users as $one)
            {
                if($one->status != 'canceled')
                {
                    $membershipTotal += $membership->total;
                    $membershipCount +=1;
                }
                
            }
        }
        
        $gifts = Gift::where([['center_id',$center->id],['status','!=','canceled']])
            ->when($request->filled('filter') && isset($request->filter['start_date']) && isset($request->filter['end_date']),
                function($query) use ($request){
                    if(!is_null($request->filter['start_date']) && !is_null($request->filter['end_date']))
                    {
                        $query->whereBetween('created_at',
                            [
                                $request->filter['start_date'],
                                $request->filter['end_date'],
                            ]);
                    }
            })->get();
        $giftsCount = 0;    
        $giftsTotal = 0; 
        foreach($gifts as $gift)
        {
            $giftsCount +=1;
            $giftsTotal += $gift->value;
        }
        
        return [
            'stat'=>true,
            'message'=>'Success',
            'payload'=>[
                'services'=>[
                        'count'=>$servicesCount,
                        'total'=>$servicesTotal
                    ],
                'memberships'=>[
                        'count' => $membershipCount,
                        'total' => $membershipTotal,
                    ],
                'gifts'=>[
                        'count'=> $giftsCount,
                        'total'=> $giftsTotal,
                    ],
                ]
            ];
    }
    
    public function salesAppointments($request)
    {
        $center = Center::find($request->center_id);
        if(!$center)
        {
            return ['stat'=>false,'message'=>'Error','payload'=>'center is not found'];
        }
        $appointments = Appointment::with([
            'member:id,first_name,last_name',
            'appointmentServices'])
            ->where('center_id',$center->id)
            ->when($request->filled('filter') && ! $request->filled('appointment_id'),
                function($query) use ($request){
                    if(isset($request->filter['start_date']) && isset($request->filter['end_date']))
                    {
                        if(!is_null($request->filter['start_date']) && !is_null($request->filter['end_date']))
                        {
                            $query->whereBetween('appointment_date',
                                [
                                    $request->filter['start_date'],
                                    $request->filter['end_date'],
                                ]);
                        }
                    }
                    
                    if(isset($request->filter['status']))
                    {
                        if(in_array($request->filter['status'],['completed','canceled','booked']))
                        {
                            $query->where('status',$request->filter['status']);
                        }
                    }
                    
                    if(isset($request->filter['member']))
                    {
                        $query->where('member_id',$request->filter['member']);
                    }
                }
            )
            ->when($request->filled('appointment_id') && ! $request->filled('filter'),
                function($query) use ($request){
                    $query->where('id',$request->appointment_id);
                }
            )
            ->get();
        return ['stat'=>true,'message'=>'success','payload'=>$appointments];
    }
    
    public function salesGifts($request)
    {
        if(! $request->filled('center_id') || ! $center = Center::find($request->center_id))
        {
            return ['stat'=>false,'message'=>'error','payload'=>'center is not found'];
        }
        
        $gifts = Gift::with('user:id,first_name,last_name,email')
        ->where('center_id',$center->id)
        ->when($request->filled('filter'),function($query)use($request){
            if(
                isset($request->filter['status']) && 
                !is_null($request->filter['status']) &&
                in_array($request->filter['status'],['valid','completed','canceled'])
            ){
                $query->where('status',$request->filter['status']);
            }
            
            if(
                isset($request->filter['start_date']) &&
                !is_null($request->filter['start_date']) &&
                isset($request->filter['end_date']) &&
                !is_null($request->filter['end_date'])
            ){
                $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
            }
        })
        ->get();
        $gifts->each(function($gift)use($center){
            $gift->ref = $gift->id.$center->id;
        });
        return ['stat'=>true,'message'=>'success','payload'=>$gifts];
        
    }
    
    public function getCenterTransactions($request)
    { 
        if(! $request->filled('center_id'))
        {
            return ['stat'=>false, 'message'=>'Error', 'payload'=>'missing required information'];
        }
        
        $center = Center::find($request->center_id);
        if(! $center || $center->status != 1)
        {
            return ['stat'=>false, 'message'=>'Error', 'payload'=>'Center is not found'];
        }
        
        $data = [];
        $appointments = Appointment::with('appointmentServices')
            ->where('center_id',$center->id)
            ->when($request->filled('filter'),function($query)use($request){
               if(
                    isset($request->filter['from_amount']) && 
                    !is_null($request->filter['from_amount']) && 
                    isset($request->filter['to_amount']) && 
                    !is_null($request->filter['to_amount'])
                ){
                    $query->whereBetween('total',[$request->filter['from_amount'],$request->filter['to_amount']]);
                } 
                
                if(
                    isset($request->filter['start_date']) &&
                    !is_null($request->filter['start_date']) &&
                    isset($request->filter['end_date']) &&
                    !is_null($request->filter['end_date'])
                ){
                    $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
                }
            })
            ->get();
        foreach($appointments as $appointment)
        {
            $data[] = [
                'created_at' => $appointment->created_at,
                'ref' => $appointment->ref,
                'client_id' => $appointment->user->id, 
                'client_name' => $appointment->user->first_name,
                'type' => 'appointment',
                'amount' => (integer)$appointment->total,
                'details' => [
                    'user' => $appointment->user,
                    'payload' => $appointment,
                    ]
                ];
        }
        
        $gifts = Gift::with('user:id,first_name,last_name,email')
            ->where('center_id',$center->id)
            ->when($request->filled('filter'),function($query)use($request){
                   if(
                        isset($request->filter['from_amount']) && 
                        !is_null($request->filter['from_amount']) && 
                        isset($request->filter['to_amount']) && 
                        !is_null($request->filter['to_amount'])
                    ){
                        $query->whereBetween('value',[$request->filter['from_amount'],$request->filter['to_amount']]);
                    } 
                    
                    if(
                        isset($request->filter['start_date']) &&
                        !is_null($request->filter['start_date']) &&
                        isset($request->filter['end_date']) &&
                        !is_null($request->filter['end_date'])
                    ){
                        $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
                    }
                })
            ->get();
        foreach($gifts as $gift)
        {
            $data[] = [
                'created_at' => $gift->created_at,
                'ref' => $gift->id.$gift->user_id.$center->id,
                'client_id' => $gift->user->id, 
                'client_name' => $gift->user->first_name,
                'type' => 'gift',
                'amount' => $gift->value,
                'details' => [
                    'user' => $gift->user,
                    'payload' => $gift,
                    ]
                ];
        }
        
        $memberships = MemberShip::with(['users:id,first_name,last_name,email','services'])
            ->where('center_id',$center->id)
            ->when($request->filled('filter'),function($query)use($request){
               if(
                    isset($request->filter['from_amount']) && 
                    !is_null($request->filter['from_amount']) && 
                    isset($request->filter['to_amount']) && 
                    !is_null($request->filter['to_amount'])
                ){
                    $query->whereBetween('total',[$request->filter['from_amount'],$request->filter['to_amount']]);
                } 
                
                if(
                    isset($request->filter['start_date']) &&
                    !is_null($request->filter['start_date']) &&
                    isset($request->filter['end_date']) &&
                    !is_null($request->filter['end_date'])
                ){
                    $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
                }
            })
            ->get();
        foreach($memberships as $membership)
        {
            $data[] = [
                'created_at' => $membership->created_at,
                'ref' => $membership->id.$membership->user_id.$center->id,
                'client_id' => null, 
                'client_name' => null,
                'type' => 'membership',
                'amount' => $membership->total,
                'details' => [
                    'users' => $membership->users,
                    'payload' => $membership,
                    ]
                ];
        }
        return ['stat'=>true, 'message'=>'success', 'payload'=>$data];
    }
    
    public function appointmentsReport($request)
    {
        return Appointment::where('center_id',$request->center_id)->with(['appointmentServices'])->get();
    }
    
    public function appointmentsByMember($request)
    {
        $members = Admin::where([['center_id',$request->center_id],['center_position','member']])
        ->get(['id','first_name','last_name']);
        foreach($members as $member)
        {
            // $member->appointments = Appointment::where('member_id',$member->id)->with(['appointmentServices'])->get();
            $appointments = Appointment::where('member_id',$member->id)
            ->when($request->filled('filter'),function($query)use($request){
                if(
                    isset($request->filter['start_date']) != null &&
                    isset($request->filter['end_date']) != null 
                ){
                    $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
                }
            })
            ->get();
            $member->appointments_count = $appointments->count();
            $member->total = $appointments->sum('total');
        }
        return ['stat'=>true,'message'=>'By Member','payload'=>$members];
    }
    
    public function appointmentsByService($request)
    {
        $services = CenterService::where('center_id',$request->center_id)->get(['id','name']);
        $data = [];
        foreach($services as $service)
        {
            $appointment_services = AppointmentServices::where('center_services_id',$service->id)
            ->withWhereHas('appointment',function($query)use($request){
                if(
                    $request->filled('filter') &&
                    isset($request->filter['start_date']) != null &&
                    isset($request->filter['end_date']) != null 
                ){
                    $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
                }
            })
            ->withCount('appointment')
            ->get();
            $count = 0;
            $total = 0;
            foreach($appointment_services as $appointment_service){
                $count += $appointment_service->appointment_count;
                $total += $appointment_service->price;
            }
            $data[] = ['id'=>$service->id,'name'=>$service->name,'appointments'=>$count,'total'=>$total];
        }
        return ['stat'=>true,'message'=>'By Service','payload'=>$data];
    }
    
    public function appointmentsByStatus($request)
    {
        $status = ['booked'=>[],'completed'=>[],'canceled'=>[],];
        foreach($status as $key=>$value)
        {
            $status[$key] = Appointment::where([
                ['center_id',$request->center_id],
                ['status',$key],
                ])
                ->when($request->filled('filter'),function($query)use($request){
                    if( isset($request->filter['start_date'],$request->filter['end_date']) ){
                        $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
                    }
                })
                ->count();
        }
        return ['stat'=>true,'message'=>'By Status','payload'=>$status];
    }
    
    public function getCenterDashboard($request)
    {
        if(! $request->filled('center_id'))
        {
            return ['stat'=>false, 'message'=>'Error', 'payload'=>'missing required information'];
        }
        
        $center = Center::find($request->center_id);
        if(! $center || $center->status != 1)
        {
            return ['stat'=>false, 'message'=>'Error', 'payload'=>'Center is not found'];
        }
        $appointments = Appointment::where('center_id',$center->id)
            ->when($request->has('filter'),function($query)use($request){
                if(
                    isset($request->filter['start_date']) &&
                    !is_null($request->filter['start_date']) &&
                    isset($request->filter['end_date']) &&
                    !is_null($request->filter['end_date'])
                ){
                    $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
                    // $query->whereBetween('appointment_date',[$request->filter['start_date'],$request->filter['end_date']]);
                }
            })
            ->get();
        $data['total'] = ['completed'=>0,'completed_amount'=>0,'booked'=>0,'canceled'=>0];
        $data['online'] = ['completed'=>0,'completed_amount'=>0,'booked'=>0,'canceled'=>0];
        $data['chart'] = [];
        $appo = [];
        foreach($appointments as $appointment)
        {
            match($appointment->status)
            {
              'completed'=>[
                  $data['total']['completed'] += 1,
                  $data['total']['completed_amount'] += $appointment->total,
              ], 
              'booked'=>$data['total']['booked'] += 1, 
              'canceled'=>$data['total']['canceled'] += 1, 
            };
            if($appointment->using_by == 'online')
            {
                match($appointment->status)
                {
                  'completed'=>[
                      $data['online']['completed'] += 1,
                      $data['online']['completed_amount'] += $appointment->total,
                  ], 
                  'booked'=>$data['online']['booked'] += 1, 
                  'canceled'=>$data['online']['canceled'] += 1, 
                };
            }
            array_push($data['chart'],['id'=>$appointment->id,'status'=>$appointment->status,'date'=>$appointment->created_at]);
            // array_push($data['chart'],['id'=>$appointment->id,'status'=>$appointment->status,'date'=>$appointment->appointment_date]);
        }  
        
        return ['stat'=>true,'message'=>'Success','payload'=>$data];
    }
    
    public function getCenterFinances($request)
    {
        if(! $request->filled('center_id'))
        {
            return ['stat'=>false, 'message'=>'Error', 'payload'=>'missing required information'];
        }
        
        $center = Center::find($request->center_id);
        if(! $center || $center->status != 1)
        {
            return ['stat'=>false, 'message'=>'Error', 'payload'=>'Center is not found'];
        }
        
        $appointment_collection = Appointment::where('center_id',$center->id)
        ->when($request->filled('filter'),function($query)use($request){
            if( isset($request->filter['start_date'],$request->filter['end_date']) ){
                $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
            }
        })
        ->get();
        $appointments = $appointment_collection->filter(function ($item) {
            return $item->using_by == 'online' && $item->status == 'completed';
        });
        $data['appointments'] = [
                'Count'=>$appointments->count(),
                'Gross'=>$appointments->sum('total'),
            ];
        
        $membership_collection = MemberShip::select(['id','name','total'])->withCount('users')->where('center_id',$center->id)
        ->when($request->filled('filter'),function($query)use($request){
            if( isset($request->filter['start_date'],$request->filter['end_date']) ){
                $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
            }
        })
        ->get();
        foreach($membership_collection as $membership)
        {
            $membership->gross = $membership->total * ($membership->users_count == 0 ? 1 : $membership->users_count);
        }
        
        $gift_collection = Gift::where('center_id',$center->id)
        ->when($request->filled('filter'),function($query)use($request){
            if( isset($request->filter['start_date'],$request->filter['end_date']) ){
                $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
            }
        })
        ->get();
        
        $data['sales'] = [
                'memberships'=>[
                    'Count'=>$membership_collection->sum('users_count'),
                    'Gross'=>$membership_collection->sum('gross'),
                ], 
                'gifts'=>[
                    'Count'=> $gift_collection->count(),
                    'Gross'=> $gift_collection->sum('value'),   
                ],
            ];
        
        return ['stat'=>true, 'message'=>'success', 'payload'=>$data];
    }
    
    public function getCenterDiscounts($request)
    {
        if(! $request->filled('center_id'))
        {
            return ['stat'=>false, 'message'=>'Error', 'payload'=>'missing required information'];
        }
        
        $center = Center::find($request->center_id);
        if(! $center || $center->status != 1)
        {
            return ['stat'=>false, 'message'=>'Error', 'payload'=>'Center is not found'];
        }
        
        $services = CenterService::where('center_id',$request->center_id)
        ->when($request->filled('filter'),function($query)use($request){
            if(isset($request->filter['start_date'],$request->filter['end_date'])){
                $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['end_date']]);
            }
        })
        ->get(['id','name','retail_price']);
        
        foreach($services as $service)
        {
            $collection = AppointmentServices::where('center_services_id',$service->id)->get(['id','price']);
            $service->count_before_offer = $collection->where('price',$service->retail_price)->count();
            $service->count_after_offer = $collection->where('price','!=',$service->retail_price)->count();
        }
        
        return ['stat'=>true, 'message'=>'success', 'payload'=>$services];
    }
    
    public function getCenterSalesByType($request)
    {
        $center = Center::findOrFail($request->center_id);
        $sales = Appointment::where([
                ['center_id',$center->id],
                ['status','completed'],
            ])
            ->when($request->filled('filter'),function($query)use($request){
                if( isset($request->filter['start_date'],$request->filter['end_date']) && 
                    !is_null($request->filter['start_date']) && 
                    !is_null($request->filter['end_date'])
                    ){
                        $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['start_date'] ]);
                    }
            })->get();
        $data = [
            'appointments'=>$sales->where('using_by','online')->sum('total'), 
            'mamberships' => $sales->where('using_by','membership')->sum('total'),    
            'gifts' => $sales->where('using_by','gift')->sum('totam'),
        ];
        return ['stat'=>true,'message'=>'success','payload'=>$data];    
    }
    
    public function getCenterSalesByService($request)
    {
        $center = Center::findOrFail($request->center_id);
        $center_services = CenterService::where('center_id',$center->id)->get(['id','name',]);
        foreach($center_services as $center_service)
        {
            $temp = AppointmentServices::where('center_services_id',$center_service->id)
            ->when($request->filled('filter'),function($query)use($request){
                if( isset($request->filter['start_date'],$request->filter['end_date']) && 
                    !is_null($request->filter['start_date']) && 
                    !is_null($request->filter['end_date'])
                    ){
                        $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['start_date'] ]);
                    }
            })
            ->get();
            $center_service->Count = $temp->count();
            $center_service->Gross = $temp->sum('price');
        }
        return ['stat'=>true,'message'=>'success','payload'=>$center_services];    
    }
    
    
    public function getCenterSalesByClient($request)
    {
        $center = Center::find($request->center_id);
        $client = Appointment::withCount('appointmentServices')
        ->where([['center_id',$center->id],['status','completed']])
        ->when($request->filled('filter'),function($query)use($request){
            if( isset($request->filter['start_date'],$request->filter['end_date']) && 
                !is_null($request->filter['start_date']) && 
                !is_null($request->filter['end_date'])
                ){
                    $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['start_date'] ]);
                }
        })
        ->pluck('user_id')
        ->unique();
        $data = [];
        foreach($client as $id)
        {
            $user = User::find($id);
            $appointments = Appointment::with('appointmentServices')
            ->where([['center_id',$center->id],['user_id',$user->id],['status','completed']])
            ->get(['id','total']);
            foreach($appointments as $appointment)
            {
                $data[] = [
                    'name' => $user->first_name.' '.$user->last_name,
                    'Gross' => $appointment->appointmentServices->sum('price'),
                    'count' => $appointment->appointmentServices->count(),
                ];
            }
        }
        return ['stat'=>true,'message'=>'success','payload'=>$data];  
    }
    
    public function getCenterSalesByTeam($request)
    {
        $center = Center::find($request->center_id);
        $members = Admin::where([['center_id',$center->id],['center_position','member']])->pluck('id');
        $data = [];
        foreach($members as $id)
        {
            $member = Admin::find($id);
            $appointments = Appointment::with('appointmentServices')
            ->where([['center_id',$center->id],['status','completed'],['member_id',$member->id]])
            ->when($request->filled('filter'),function($query)use($request){
                if( isset($request->filter['start_date'],$request->filter['end_date']) && 
                    !is_null($request->filter['start_date']) && 
                    !is_null($request->filter['end_date'])
                    ){
                        $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['start_date'] ]);
                    }
            })
            ->get(['id','total']);
            $gross = 0;
            $count = 0;
            foreach($appointments as $appointment)
            {
                $gross += $appointment->appointmentServices->sum('price');
                $count += $appointment->appointmentServices->count();
            }
            $data[] = [
                'name' => $member->first_name.' '.$member->last_name,
                'Gross' => $gross,
                'Count' => $count,
            ];
        }
        return ['stat'=>true,'message'=>'success','payload'=>$data];  
    }
    
    public function getCenterSalesByDate($request)
    {
        $center = Center::findOrFail($request->center_id);
        $sales = Appointment::where([
                ['center_id',$center->id],
                ['status','completed'],
            ])
            ->when($request->filled('filter'),function($query)use($request){
                if( isset($request->filter['start_date'],$request->filter['end_date']) && 
                    !is_null($request->filter['start_date']) && 
                    !is_null($request->filter['end_date'])
                    ){
                        $query->whereBetween('created_at',[$request->filter['start_date'],$request->filter['start_date'] ]);
                    }
            })->get();
        $data = [
            'appointments' =>[
                'Gross'=> $sales->where('using_by','online')->sum('total'),
                'Count'=>$sales->where('using_by','online')->count(),
                ], 
            'mamberships' =>[
                'Gross'=> $sales->where('using_by','membership')->sum('total'),
                'Count'=>$sales->where('using_by','membership')->count(),
                ],    
            'gifts' =>[
                'Gross'=> $sales->where('using_by','gift')->sum('totam'),
                'Count'=>$sales->where('using_by','gift')->count(),
                ],
        ];
        return ['stat'=>true,'message'=>'success','payload'=>$data];
    }
}