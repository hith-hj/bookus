<?php

namespace App\Http\Controllers\Admin;
use App\Repositories\CenterCategoryRepository;

use App\Http\Controllers\ApiController;
use App\Models\CenterCategory;
use App\Models\Admin;
use App\Models\CenterService;
use Illuminate\Http\Request;
//CenterCategoryController
class CenterCategoryController extends ApiController
{

    private $centerCategoryRepository;

    public function __construct(CenterCategoryRepository $centerCategoryRepository)
    {
        $this->centerCategoryRepository = $centerCategoryRepository;
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
        $categories = $this->centerCategoryRepository->getCategories($request)->paginate($limit);

        return $this->respondSuccess($categories->all(), $this->createApiPaginator($categories));


    }
    public function store(Request $request)//: RedirectResponse
    {
        $this->centerCategoryRepository->add($request);
        return response()->json([
            'result' => 'success',
        ], 200);

    }
    public  function update(Request $request, $id)
    {
        $centerCategory=CenterCategory::find($id);
        $this->centerCategoryRepository->update($request,$centerCategory);
        return response()->json([
            'result' => 'success',
        ], 200);
    }
    public function show( $id)
    {
    $centerDetails= $this->centerCategoryRepository->getCenterCategory($id);

        return $this->respondSuccess(
            [
                'centerCategory' => $centerDetails
            ]
        );
    }

    public function destroy(Request $request, CenterCategory $centerCategory)
    {

        if (!is_null($centerCategory)) {
            $this->centerCategoryRepository->delete($centerCategory);
            return response()->json([
                'result' => 'success',
                'message' => ''
            ], 200);
        }
        return response()->json([
            'result' => 'failed',
            'message' => 'centerCategory not found',
        ], 500);
    }
    public function getAllCenterCategory()
    {


      $admin= Admin::whereId( auth('sanctum')->id())->with('center')->first();
     $center =$admin->center;
        $categories = $this->centerCategoryRepository->getAllCategories($center);

        return $this->respondSuccess([
            'categories'=>$categories,
        ]);


    }

    public function getTreatment(){
        $admin= Admin::whereId( auth('sanctum')->id())->with('center')->first();
        $center =$admin->center;
         $categories =       CenterCategory::query()->where('center_id',$center->id)->pluck('id');
         $treatments= CenterService::whereIn('center_category_id', $categories)->pluck('name');
        return $this->respondSuccess([
            'treatment'=>$treatments
        ]);
    }
public function newCenterCategory(Request $request ) {
    $request->validate(
        [
            'name' => 'required',
            'status' => 'required',
              ],
        [
          'name.required' => 'name is Required', // custom message
          'status.required' => 'status is Required', // custom message
        ]
    );
    $category = new CenterCategory(populateModelData($request, CenterCategory::class));
        $category->center_id=$this->getCenter();
        $category->save();
        return $this->respondSuccess();
}

public function getCenterCategory($id){
   $category= $this->centerCategoryRepository->getCategory($id);
    return $this->respondSuccess([
        'category'=>$category
    ]);
}
}
