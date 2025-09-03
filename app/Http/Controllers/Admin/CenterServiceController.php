<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CenterServiceRequest;
use App\Models\CenterService;
use App\Repositories\CenterServiceRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\ApiController;

class CenterServiceController extends ApiController
{
    private $centerServiceRepository;

    public function __construct(CenterServiceRepository $centerServiceRepository)
    {
//        appendGeneralPermissions($this);
        $this->centerServiceRepository = $centerServiceRepository;

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
        $centerServices =$this->centerServiceRepository->getCenterServices($request)->paginate($limit);

        return $this->respondSuccess($centerServices->all(), $this->createApiPaginator($centerServices));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
//        return view('centerService.crud.edit-new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CenterServiceRequest $request
     * @return RedirectResponse
     */
    public function store(Request $request)//: RedirectResponse
    {
$this->centerServiceRepository->add($request);
return response()->json([
    'result' => 'success',
], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param CenterService $centerService
     * @return Factory|\Illuminate\Contracts\View\View
     */
    public function show(CenterService $centerService)
    {
//        return view('centerService.centerService.show', compact('centerService'));
    }

public function newCenterServices(Request $request){
      $center_id=  $this->getCenter();
    $serviceCenter= new CenterService();
    $serviceCenter->name=$request->get('name');
    $serviceCenter->Treatment_type=$request->get('Treatment_type');
    $serviceCenter->center_category_id=$request->get('category_id');
    $serviceCenter->Aftercare_description=$request->get('Aftercare_description');
    $serviceCenter->description=$request->get('description');
    $serviceCenter->service_gender=$request->get('service_gender');
    $serviceCenter->online_booking=$request->get('online_booking') == "true"?true:false;
    $serviceCenter->Duration=$request->get('Duration');
    $serviceCenter->retail_price=$request->get('retail_price');
    $serviceCenter->price_type=$request->get('price_type');
    $serviceCenter->center_id=$center_id;
    $serviceCenter->extra_time=$request->get('extra_time')=="true"?true:false;
    $serviceCenter->save();
    return $this->respondSuccess();



}
public function delete($id) {
    $centerService =CenterService::find($id)->delete();
    return $this->respondSuccess();
}

}
