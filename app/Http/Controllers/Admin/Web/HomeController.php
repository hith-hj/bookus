<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    
    public function home(Request $request)
    {        
        return view('admin.home');
    }

    

}
