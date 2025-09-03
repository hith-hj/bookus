<?php

namespace App\Http\Controllers\User;

use App\Models\Admin;
use App\Models\Notification;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class AuthController extends Controller
{

    public function registerPage(){
        return view('user.auth.register');
    }
    
    public function register(Request $request){
        $data = $request->validate([
            'first_name' => ['required','string','between:3,20'],
            'last_name' => ['required','string','between:3,20'],
            'phone_number' => ['required','regex:/^[+][1-9]{1,4}[0-9]{6,9}$/','unique:users'],
            'password' => ['required','confirmed','string','min:6'],
            'email' => ['required','email','unique:users'],
        ],['phone_number:regex'=>'phone number format is invalid']);
        $user = User::create($data);
        $user->encryptPassword()->save();
        Auth::guard('web')->login($user);
        if(Auth::check()) {
            $request->session()->regenerate();
            return redirect()->route('user.home');
        }else{
            $user->delete();
            return back()->with('error','something went wrong');
        }
    }
    
    public function loginPage(){
        return view('user.auth.login');
    }
    
    public function login(Request $request){
        $credentials = $request->validate([
                'email'=>['required','email','exists:users,email'],
                'password'=>['required','min:6',],
            ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('user.home');
        }
        return back()->with('error','The provided credentials do not match our records.')->onlyInput('email');
    }
    
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}