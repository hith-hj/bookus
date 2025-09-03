<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index(Request $request){
        $clients = User::query()
        ->when($request->filled('search'),function($query) use($request){
            return $query->where('first_name','like','%'.$request->search.'%')
            ->orWhere('last_name','like','%'.$request->search.'%');
        })->when($request->filled('filter'),function($query) use($request){
            if(in_array($request->filter,['male','female'])){
                return $query->where('gender',$request->filter);
            }
        })->paginate(20);
        return view('admin.pages.clients',['clients'=>$clients]);
    }

    public function client(Request $request, $id){
        $client = User::with(['appointments'])->findOrFail($id);
        return view('admin.pages.client',['client'=>$client]);
    }

    public function store(Request $request){
        $request->validate([
            'first_name'=>['required','string','min:4','max:20'],
            'last_name'=>['required','string','min:4','max:20'],
            'email'=>['required','email','unique:users,email'],
            'phone_number'=>[
                'required',
                'regex:/^[+][1-9]{1,4}[0-9]{6,9}$/',
                'unique:users,phone_number'
            ],
            'password'=>['required','min:6'],
            'gender'=>['nullable','in:male,female'],
            'birth_date'=>['nullable','date_format:Y-m-d'],
            'image'=>['nullable','image'],
        ]);
        // $request->dd();
        $user = User::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            'phone_number'=>$request->phone_number,
            'gender'=>$request->gender,
            'birth_date'=>$request->birth_date,
            'password' => Hash::make($request->password),
        ]);
        if ($request->hasFile('image')) {
            $user->image = Storage::disk('public')->put('users', $request->file('image'));
            $user->save();
        }
        return redirect()->route('admin.clients')->with('success','client created');
    }

    public function edit(Request $request, $id){
        $request->validate([
            'first_name'=>['required','string','min:4','max:20'],
            'last_name'=>['required','string','min:4','max:20'],
            'email'=>['required','email',],
            'phone_number'=>['required','regex:/^[+][1-9]{1,4}[0-9]{6,9}$/',],
            'image'=>['nullable','image'],
        ],['phone_number:regex'=>'phone number format is invalid']);
        $user = User::findOrFail($id);

        if($user->email != $request->email && User::where('email',$request->email)->count() > 0){
            return back()->with('error','Email already taken');
        }
        if($user->phone_number != $request->email && User::where('phone_number',$request->email)->count() > 0){
            return back()->with('error','Email already taken');
        }
        if ($request->hasFile('image')) {
            if ($user->image != null) {
                $user->image = Storage::disk('public')->delete($user->image);
            }
            $user->image = Storage::disk('public')->put('users', $request->file('image'));
            $user->save();
        }
        $user->update($request->only(['first_name','last_name','email','phone_number','birth_date']));
        return redirect()->route('admin.clients')->with('success','client created');
    }

    public function delete($id){
        $user = User::findOrFail($id);
        $user->appointments()->delete();
        $user->addresses()->delete();
        $user->favorites()->delete();
        $user->reviews()->delete();
        $user->gifts()->delete();
        // $user->memberships()->delete();
        $user->delete();
        return redirect()->route('admin.clients')->with('success','client deleted');
    }
}