<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\View\View;

class SettingController extends ApiController
{
    private $settingRepository;
    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit') ?: 10;
        if ($limit > 30) $limit = 30;
        $settings = $this->settingRepository->getSettings($request);
        return $this->respondSuccess($settings);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
//        return view('admin.crud.edit-new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SettingRequest $request
     * @return RedirectResponse
     */
    public function store(SettingRequest $request)//: RedirectResponse
    {

    }

    /**
     * Display the specified resource.
     *
     * @param Setting $setting
     * @return Factory|\Illuminate\Contracts\View\View
     */
    public function show(Setting $setting)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Setting $setting
     * @return Application|Factory|View
     */
    public function edit(Setting $setting)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SettingRequest $request
     * @param Setting $setting
     * @return RedirectResponse
     */
    public function update(SettingRequest $request, Setting $setting)//: RedirectResponse
    {
        $this->settingRepository->update($request,$setting);
        return $this->respondSuccess();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Setting $setting
     * @return RedirectResponse
     */
    public function destroy(Request $request, Setting $setting)//: RedirectResponse
    {

    }

    public function updateSetting(Request $request){
        $setting =Setting::query()->first();

    }


}
