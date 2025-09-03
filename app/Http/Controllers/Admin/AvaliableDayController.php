<?php

namespace App\Http\Controllers\AvaliableDay;

use App\Http\Controllers\Controller;
use App\Models\AvaliablDay;
use Illuminate\Http\Request;
use App\Http\Requests\AvalibleDayRequest;
use App\Models\Center;
use App\Repositories\AvalibleDayRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AvaliableDayController extends Controller
{
    private $avaliableDay;
    public function __construct(AvaliablDay $avaliableDay)
    {
        $this->avaliableDay = $avaliableDay;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
//        return view('avaliableDay.crud.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
//        return view('avaliableDay.crud.edit-new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AvaliableDayRequest $request
     * @return RedirectResponse
     */
    public function store(AvalibleDayRequest $request)//: RedirectResponse
    {

    }

    /**
     * Display the specified resource.
     *
     * @param AvaliableDay $avaliableDay
     * @return Factory|\Illuminate\Contracts\View\View
     */
    public function show(AvaliableDay $avaliableDay)
    {
//        return view('avaliableDay.avaliableDay.show', compact('avaliableDay'));
    }



}
