<?php

namespace App\Repositories;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminRepository
{

    public function add(Request $request)
    {

        $admin = new Admin($request->except(['password']));

        if ($password = $request->get('password'))
            $admin->password = bcrypt($password);

        if ($request->hasFile('cover_image'))
            $admin->cover_image = Storage::disk('public')->put('admins', $request->file('cover_image'));

        //        $admin->syncRoles($request->get('role'));

        $admin->save();
    }

    public function update(Request $request, Admin $admin, $updateFromAdmin = true)
    {
        $admin->update(populateModelData($request, Admin::class));
        if ($request->hasFile('cover_image')) {
            // if there is an old cover_image delete it
            if ($admin->cover_image != null) {
                $admin->cover_image = Storage::disk('public')->delete($admin->cover_image);
            }
             // store the new image
            $admin->cover_image = Storage::disk('public')->put('admins', $request->file('cover_image'));
        }
        $admin->save();


        return $admin;
    }

    public function delete(Admin $admin)
    {
        if ($admin->cover_image != null)
            $admin->cover_image = Storage::disk('public')->delete($admin->cover_image);

        $admin->delete();
    }

    public function getAdmins(Request $request, $user = null): Builder
    {
        $admins = Admin::with('roles');
        if ($status = $request->get('status')) {
            if ($status == 'inactive')
                $admins->where('status', 0);
            else
                $admins->where('status', 1);
        }

        if ($search = $request->get('search')) {
            $admins->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        return $admins->orderBy('created_at');
    }

    public function getAdminsDataTable(Request $request): LengthAwarePaginator
    {

        $admins = Admin::query();

        if ($request->has('query')) {
            if (isset($request->get('query')['status']) != null)
                $admins->where('status', $request->get('query')['status']);

            if (isset($request->get('query')['role']) != null) {
                $admins = $admins->role($request->get('query')['role']);
            }

            if ($role = $request->get('vendor_role')) {
                $admins = $admins->role($role);
            }

            if (isset($request->get('query')['from_date']) != null)
                $admins->where('created_at', '>=', $request->get('query')['from_date']);

            if (isset($request->get('query')['to_date']) != null)
                $admins->where('created_at', '<=', Carbon::parse($request->get('query')['to_date'])->endOfDay());


            if (isset($request->get('query')['search']) != null) {
                $tokens = convertToSeparatedTokens($request->get('query')['search']);
                $admins->whereRaw("MATCH(name, email, username) AGAINST(? IN BOOLEAN MODE)", $tokens);
            }
        }

        if ($request->has('sort')) {
            $field = $request->get('sort')['field'];
            if (!in_array($field, app(Admin::class)->getFillable())) $field = 'name';
            $admins = $admins->orderBy($field, $request->get('sort')['sort'] ?? 'asc')
                ->paginate($request->get('pagination')['perpage'], ['*'], 'page', $request->get('pagination')['page']);
        } else
            $admins = $admins->orderBy('id', 'desc')
                ->paginate($request->get('pagination')['perpage'], ['*'], 'page', $request->get('pagination')['page']);

        return $admins;
    }
}
