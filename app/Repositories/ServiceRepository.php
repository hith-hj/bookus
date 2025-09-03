<?php

namespace App\Repositories;

use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class  ServiceRepository
{

    public function add(Request $request)//: User
    {
        $category = new Service(populateModelData($request, Service::class));
        $category->save();
    }

    public function update(Request $request, Service $service)
    {
    }

    public function delete(Service $service)
    {

    }
    public function getServices(Request $request)  {
        $services = Service::query();
        if ($search = $request->get('search')) {
            $services->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')    ;
            });}



        return $services->latest();
    }


}
