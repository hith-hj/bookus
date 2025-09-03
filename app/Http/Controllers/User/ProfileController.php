<?php 

namespace App\Http\Controllers\User;

use App\Models\Admin;
use App\Models\User;
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

class ProfileController extends Controller
{

    public function profile($id = null){
        $user = is_null($id) ? auth()->user() : User::findOrFail($id);
        return view('user.pages.profile',['user'=>$user]);
    }
    
    
    public function edit(Request $request){
        $allowedDate = Carbon::now()->subYear(12);
        $data = $request->validate([
            'first_name'=>['required','string','between:3,20'],
            'last_name'=>['required','string','between:3,20'],
            'gender'=>['required','in:male,female'],
            'birth_date'=>['required','date_format:Y-m-d',"before:$allowedDate"],
        ],[
            'birth_date.before'=>'You must be older than 12 years old',
            ]);
        auth()->user()->update($data);
        return back()->with('success','Your Information is updated');
    }
    
}