<?php

namespace App\Http\Controllers\Center;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Center\Repos\CenterClientsRepository as ClientRepo;
use App\Models\Category;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Center;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Models\Contact;
use App\Models\OpenDay;
use App\Models\MemberShip;
use App\Models\User;
use App\Models\Note;
use App\Models\Review;
use App\Models\Gift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

class CenterClientsController extends ApiController
{
    private $clientRepo;
    
    public function __construct(ClientRepo $clientRepo)
    {
        $this->clientRepo = $clientRepo;
    }
    
    public function getCenterClients(Request $request)
    {
        // $clients = $clientsQuery->paginate($request->perPage ?? 10,['id','first_name','last_name','email','status']);
        if(!$request->has('center_id')){
            return $this->resBack(['stat'=>false,
            'message'=>'Error',
            'payload'=>'Center id is required']);
        }
        $clients = $this->clientRepo->centerClientsQuery($request);;
        if(count($clients)<=0){
            $status = false;
            $message = 'No Clients Yet';
            $payload = 'Nothing found';
        }else{
            $status = true;
            $message = 'Clients List';
            $payload = $clients;
        }
        $stat = [
            'stat'=>$status,
            'message'=>$message,
            'payload'=>$payload
        ];
        
        return $this->resBack($stat,'Client_list');
        
    }
    
    public function getClientDetails(Request $request)
    {
        if(is_null($request->client_id)){
            return $this->resBack([
                'stat'=>false,
                'message'=>'id Error',
                'payload'=>'user id is required'
                ]);
        }
        $stat = $this->clientRepo->centerClientDetails($request);
        return $this->resBack($stat,'Client_details'); 
    }
    
    public function createCenterClient(Request $request)
    {
        $validator = $this->clientRepo->validateCenterClientDetails($request->all());
        if($validator->fails()){
            return $this->resBack([
                'stat'=>false,
                'message'=>$validator->errors()->first(),
                'payload'=>$validator->errors()
                ]);
        }
        
        $stat = $this->clientRepo->addCenterClient($request);
        return $this->resBack($stat,'Client_details'); 
    }
    
    public function editCenterClient(Request $request)
    {
        $validator = $this->clientRepo->validateCenterClientUpdteDetails($request->all());
        if($validator->fails()){
            return $this->resBack([
                    'stat'=>false,
                    'message'=>$validator->errors()->first(),
                    'payload'=>$validator->errors()
                ]);
        }
        $stat = $this->clientRepo->updateCenterClient($request);
        return $this->resBack($stat,'Client');        
    }
    
    public function deleteCenterClient(Request $request)
    {
        $stat = $this->clientRepo->deleteCenterClient($request);
        return $this->resBack($stat,'Client');
    }
    
    public function getCenterNote(Request $request)
    {
        $stat = $this->clientRepo->getCenterNote($request);
        return $this->resBack($stat,'note');
    }
    
    public function createCenterClientNote(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'center_id'=>['required','exists:centers,id',],
            'user_id'=>['required','exists:users,id',],
            'note'=>['required','string','max:1000'],
            ]);
        if($validator->fails()){
            return $this->resBack([
                'stat'=>false,
                'message'=>$validator->errors()->first(),
                'payload'=>$validator->errors(),
                ]);
        }
        $stat = $this->clientRepo->addCenterClientNote($request);
        return $this->resBack($stat,'Note');
    }
    
    public function deleteCenterClientNote(Request $request)
    {
        $note = Note::where([['id',$request->note_id],['center_id',$request->center_id]])->first();
        if(!$note){
            return $this->resBack(['stat'=>false,'message'=>'Note Error','payload'=>'Note Not Found']);
        }
        $note->delete();
        return $this->resBack(['stat'=>true,'message'=>'success','payload'=>$note],'Note Deleted');
    }
    
    public function getCenterMemberships(Request $request)
    {
        if(!$request->has('center_id')){
            return $this->resBack(['stat'=>false,
            'message'=>'Error',
            'payload'=>'Center id is required']);
        }
        $memberships = $this->clientRepo->getCenterMemberships($request);
        if(count($memberships)<=0){
            $status = false;
            $message = 'No Memberships Yet';
            $payload = 'Nothing found';
        }else{
            $status = true;
            $message = 'Memberships List';
            $payload = $memberships;
        }
        $stat = [
            'stat'=>$status,
            'message'=>$message,
            'payload'=>$payload
        ];
        return $this->resBack($stat,'Memberships_list');
    }
    
    public function createCenterMembership(Request $request)
    {
        $validator = $this->clientRepo->validateMembershipDetails($request->all());
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'errors'=>$validator->errors()]);
        }
        $stat = $this->clientRepo->addCenterMembership($request); 
        return $this->resBack($stat,'Membership'); 
    }
    public function cancelCenterMembership(Request $request)
    {
        $stat = $this->clientRepo->cancelCenterMembership($request);
        return $this->resBack($stat,'Membership');
    }
    public function createClientMembership(Request $request)
    {
        $validator = $this->clientRepo->validateMembershipClient($request->all());
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'errors'=>$validator->errors()]);
        }
        $stat = $this->clientRepo->addClientMembership($request);
        return $this->resBack($stat,'Membership');
    }
    
    public function cancelClientMembership(Request $request)
    {
        $stat = $this->clientRepo->cancelClientMembership($request);
        return $this->resBack($stat,'MemberShip');
    }
    
    public function getCenterGifts(Request $request)
    {
        if(!$request->has('center_id')){
            return $this->resBack(['stat'=>false,
            'message'=>'Error',
            'payload'=>'Center id is required']);
        }
        $gifts = $this->clientRepo->getCenterGifts($request);
        if(count($gifts)<=0){
            $status = false;
            $message = 'No Gifts Yet';
            $payload = 'Nothing found';
        }else{
            $status = true;
            $message = 'Gifts List';
            $payload = $gifts;
        }
        $stat = [
            'stat'=>$status,
            'message'=>$message,
            'payload'=>$payload
        ];
        
        return $this->resBack($stat,'Gifts_list');
        
    }
    
    public function createClientGift(Request $request)
    {
        $validator = $this->clientRepo->validateGiftDetails($request->all());
        if($validator->fails()){
            return $this->respondError(['message'=>$validator->errors()->first(),'errors'=>$validator->errors()]);
        }
        $stat = $this->clientRepo->addClientGift($request);
        return $this->resBack($stat,'Gift');
    }
    
    public function cancelClientGift(Request $request)
    {
        $stat = $this->clientRepo->cancelClientGift($request);
        return $this->resBack($stat,'Gift');
    }
    
    public function checkUserExists(Request $request)
    {
        $stat = $this->clientRepo->checkUserExists($request);
        return $this->resBack($stat,'User');
    }
    
    
    public function resBack($res,$payload_title = 'payload')
    {
        return $res['stat'] == false?
            $this->respondError(['message'=>$res['message'],'Errors'=>$res['payload']]) :
            $this->respondSuccess(['message'=>$res['message'],$payload_title=>$res['payload']]);
    }
}