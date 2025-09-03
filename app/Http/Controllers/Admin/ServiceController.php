<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Repositories\ServiceRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\ApiController;

class ServiceController extends ApiController
{
    private $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
//        appendGeneralPermissions($this);
        $this->serviceRepository = $serviceRepository;

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
        $services =$this->serviceRepository->getServices($request)->paginate($limit);

        return $this->respondSuccess($services->all(), $this->createApiPaginator($services));
   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
//        return view('service.crud.edit-new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequest $request
     * @return RedirectResponse
     */
    public function store(Request $request)//: RedirectResponse
    {
$this->serviceRepository->add($request);
return response()->json([
    'result' => 'success',
], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param Service $service
     * @return Factory|\Illuminate\Contracts\View\View
     */
    public function show(Service $service)
    {
//        return view('service.service.show', compact('service'));
    }



}
