<?php

namespace App\Repositories;

use App\Models\CenterService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class  CenterServiceRepository
{

    public function add(Request $request)//: User
    {
        $category = new CenterService(populateModelData($request, Category::class));
        $category->save();
    }

    public function update(Request $request, CenterService $service)
    {
    }

    public function delete(CenterService $service)
    {

    }
    public function getCenterServices(Request $request)  {
        $services = CenterService::query();
        if ($search = $request->get('search')) {
            $services->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')    ;
            });}



        return $services->latest();
    }


}
