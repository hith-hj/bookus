<?php

namespace App\Http\Controllers\Center;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Center\Repos\CenterReportsRepository as CenterRepo;
use App\Models\Category;
use App\Models\Service;
use App\Models\Center;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Models\Contact;
use App\Models\OpenDay;
use Carbon\carbon;


class CenterReportsController extends ApiController
{
    private $repo;
    public function __construct()
    {
        $this->repo = new CenterRepo();
    }
    
    public function salesIndex(Request $request)
    {
        if(!$request->filled('center_id'))
        {
            return ['stat'=>false,'message'=>'Error','payload'=>'center id is required'];
        }
        
        return $this->resBack($this->repo->salesReport($request),'Sales');
    }
    
    public function salesAppointments(Request $request)
    {
        if(!$request->filled('center_id'))
        {
            return ['stat'=>false,'message'=>'Error','payload'=>'center id is required'];
        }
        
        return $this->resBack($this->repo->salesAppointments($request),'Sales Appointments');
    }
    
    public function appointmentsIndex(Request $request)
    {
        if(!$request->filled('center_id'))
        {
            return $this->resBack(['stat'=>false,'message'=>'Error','payload'=>'center id is required']);
        }
        
        if(!$request->filled('report_type'))
        {
            $appos = $this->repo->appointmentsReport($request);
            return $this->resBack(['stat'=>true,'message'=>'Reports','payload'=>$appos]);
        }
        
        $type = $request->report_type;
        $stat = match($type){
            'members'=>$this->repo->appointmentsByMember($request),
            'services'=>$this->repo->appointmentsByService($request),
            'status'=>$this->repo->appointmentsByStatus($request),
            default=>['stat'=>false,'message'=>'error','payload'=>'No Type Match'],
        };
        return $this->resBack($stat,'Reports');
    }
    
    public function getCenterTransactions(Request $request)
    {
        return $this->resBack($this->repo->getCenterTransactions($request),'Transactions');
    }
    
    public function salesGifts(Request $request)
    {
        return $this->resBack($this->repo->salesGifts($request),'Sales Gifts');
    }
    
    public function getCenterDashboard(Request $request)
    {
        return $this->resBack($this->repo->getCenterDashboard($request),'Dashboard');
    }
    
    public function getCenterFinances(Request $request)
    {
        return $this->resBack($this->repo->getCenterFinances($request),'Finances');
    }
    
    public function getCenterDiscounts(Request $request)
    {
        return $this->resBack($this->repo->getCenterDiscounts($request),'Discounts');
    }
    
    public function getCenterSales(Request $request)
    {
        if(!$request->filled('center_id') || is_null($request->center_id))
        {
            return $this->resBack([
                'stat'=>false,
                'message'=>'error',
                'payload'=>'center is not found'
            ],'sales');
        }
        
        if($request->filled('sales_type')){
            return match($request->sales_type){
                'type'=>$this->resBack($this->repo->getCenterSalesByType($request),'by type'),
                'service'=>$this->resBack($this->repo->getCenterSalesByService($request),'by service'),
                'client'=>$this->resBack($this->repo->getCenterSalesByClient($request),'by client'),
                'branch'=>$this->resBack($this->repo->getCenterSalesByBranch($request),'by branch'),
                'team'=>$this->resBack($this->repo->getCenterSalesByTeam($request),'by team'),
                'date'=>$this->resBack($this->repo->getCenterSalesByDate($request),'by date'),
                default=>$this->resBack($this->repo->getCenterSalesByType($request),'sales'),
            };
        }
        return $this->resBack($this->repo->getCenterSalesByType($request),'sales');
    }
    
    
    
    
    
    
    
}