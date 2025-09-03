<?php

namespace App\Repositories;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingRepository
{

    public function add(Request $request)//: Setting
    {

    }

    public function update(Request $request, Setting $setting)
    {
        $setting->update(populateModelData($request, Setting::class));
        $setting->save();
    }

    public function delete(Setting $setting)
    {

    }
    public function getSettings(Request $request)
    {
        $settings =Setting::first();


        return $settings ;
     }

}
