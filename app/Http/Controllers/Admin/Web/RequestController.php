<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index(Request $request){
        $requests = DB::table('admin_requests')->get();
        foreach($requests as $request){
            $requesterModel = $request->requester_type;
            $request->requester = $requesterModel::findOrFail($request->requester_id);
            $requestedModel = $request->requested_type;
            $request->requested = $requestedModel::findOrFail($request->requester_id);
        }
        return view('admin.pages.requests',['requests'=>$requests]);
    }
    
}