<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserRepository
{

    public function add(Request $request)//: User
    {
        $user = new User($request->except(['password']));

        if ($password = $request->get('password'))
            $user->password = bcrypt($request->first_name);

        if ($request->hasFile('image'))
            $user->image = Storage::disk('public')->put('users', $request->file('image'));

        $user->save();
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => ['required','string','min:2','max:20'],
            'last_name' => ['required','string','min:2','max:20'],
            'email' => ['required','email:dns,rfc'],
            'gender' => ['nullable','in:Female,Male'],
            'birth_date' => ['nullable','date_format:Y-m-d'],
            ]);
        $user->update(populateModelData($request, User::class));
        if ($request->hasFile('image')) {
            if ($request->hasFile('image')) {
                if ($user->image != null) {
                    $user->image = Storage::disk('public')->delete($user->image);
                }
            }
            $user->image = Storage::disk('public')->put('users', $request->file('image'));
        }
        $user->save();
    }

    public function delete(User $user)
    {
        $user->delete();

    }

  

    public function getUsers(Request $request): Builder
    {
        $users = User::query();
        if ($search = $request->get('search')) {
            $users->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });}



        return $users->latest();
    }


}
