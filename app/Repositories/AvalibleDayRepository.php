<?php

namespace App\Repositories;

use App\Models\AvaliableDay;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvalibleDayRepository {

    public function add(Request $request)
    {
        $avaliableDay = new AvaliableDay($request->except(['password']));

        if ($password = $request->get('password'))
            $avaliableDay->password = bcrypt($password);

        if ($request->hasFile('avatar'))
            $avaliableDay->avatar = Storage::disk('public')->put('admins', $request->file('avatar'));

        if ($request->hasFile('cover_image'))
            $avaliableDay->cover_image = Storage::disk('public')->put('admins', $request->file('cover_image'));

        $avaliableDay->syncRoles($request->get('role'));

        $avaliableDay->save();
    }

    public function update(Request $request, AvaliableDay $avaliableDay, $updateFromAvaliableDay = true)
    {
        if ($password = $request->get('password'))
            $avaliableDay->password = bcrypt($password);

        if ($updateFromAvaliableDay){
            foreach ($avaliableDay->roles()->get() as $role)
                $avaliableDay->removeRole($role);

            $avaliableDay->syncRoles($request->get('role'));
        }

        $avaliableDay->update($request->except(['password']));

        if ($request->hasFile('avatar')){
            // if there is an old avatar delete it
            if ($avaliableDay->avatar != null){
                $avaliableDay->avatar = Storage::disk('public')->delete($avaliableDay->avatar);
            }

            // store the new image
            $avaliableDay->avatar = Storage::disk('public')->put('admins', $request->file('avatar'));
        }

        if ($request->hasFile('cover_image')){
            // if there is an old cover_image delete it
            if ($avaliableDay->cover_image != null){
                $avaliableDay->cover_image = Storage::disk('public')->delete($avaliableDay->cover_image);
            }

            // store the new image
            $avaliableDay->cover_image = Storage::disk('public')->put('admins', $request->file('cover_image'));
        }


        $avaliableDay->save();


    }

    public function delete(AvaliableDay $avaliableDay)
    {
        if ($avaliableDay->image != null)
            $avaliableDay->image = Storage::disk('public')->delete($avaliableDay->image);

        $avaliableDay->delete();
    }

    public function getAvaliableDays(Request $request, $user = null): Builder
    {
        $avaliableDays = AvaliableDay::query();

        if ($role = $request->get('role')){
            $avaliableDays = $avaliableDays->role($role);
        }

        if ($status = $request->get('status')){
            $avaliableDays = $avaliableDays->where('status', $status);
        }

        if ($review = $request->get('review')){
            $avaliableDays->where('rate', $review);
        }

        if ($request->has('sort_by_review') && $request->get('sort_by_review') != null){
            $asc = $request->get('sort_by_review');
            $asc = $asc == 1 ? 'asc' : 'desc';
            $avaliableDays->orderBy('rate', $asc);
        }

        if ($request->has('latitude') && $request->has('longitude')){
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');

            $avaliableDays->selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
//                ->having('distance', '<', 25)
                ->orderBy('distance');
        }else if ($request->has('nearest') && $request->get('nearest') != null && $user != null){
            $latitude = $user->latitude;
            $longitude = $user->longitude;

            $avaliableDays->selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
//                ->having('distance', '<', 25)
                ->orderBy('distance');
        }

        if ($search = $request->get('search')){
            $tokens = convertToSeparatedTokens($search);
            $avaliableDays->whereRaw("MATCH(name, email, username) AGAINST(? IN BOOLEAN MODE)", $tokens);
        }
        return $avaliableDays->orderBy('created_at');

    }

    public function getAvaliableDaysDataTable(Request $request): LengthAwarePaginator
    {

        $avaliableDays = AvaliableDay::query();

        if ($request->has('query')){
            if (isset($request->get('query')['status']) != null)
                $avaliableDays->where('status' , $request->get('query')['status']);

            if (isset($request->get('query')['role']) != null){
                $avaliableDays = $avaliableDays->role($request->get('query')['role']);
            }

            if ($role = $request->get('vendor_role')){
                $avaliableDays = $avaliableDays->role($role);
            }

            if (isset($request->get('query')['from_date']) != null)
                $avaliableDays->where('created_at' ,'>=', $request->get('query')['from_date']);

            if (isset($request->get('query')['to_date']) != null)
                $avaliableDays->where('created_at' ,'<=', Carbon::parse($request->get('query')['to_date'])->endOfDay());


            if (isset($request->get('query')['search']) != null){
                $tokens = convertToSeparatedTokens($request->get('query')['search']);
                $avaliableDays->whereRaw("MATCH(name, email, username) AGAINST(? IN BOOLEAN MODE)", $tokens);
            }
        }

        if ($request->has('sort')){
            $field = $request->get('sort')['field'];
            if (!in_array($field, app(AvaliableDay::class)->getFillable())) $field = 'name';
            $avaliableDays = $avaliableDays->orderBy($field, $request->get('sort')['sort'] ?? 'asc')
                ->paginate($request->get('pagination')['perpage'],['*'], 'page',$request->get('pagination')['page']);
        }
        else
            $avaliableDays = $avaliableDays->orderBy('id', 'desc')
                ->paginate($request->get('pagination')['perpage'],['*'], 'page',$request->get('pagination')['page']);

        return $avaliableDays;
    }


}
