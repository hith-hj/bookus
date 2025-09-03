<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\ResourceRepository;
use Illuminate\Http\Request;

class ResourceController extends ApiController
{

    private $resourceRepository;

    public function __construct(ResourceRepository $resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
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
        $resources = $this->resourceRepository->getResources($request)->paginate($limit);

        return $this->respondSuccess($resources->all(), $this->createApiPaginator($resources));


    }
    public function store(Request $request)//: RedirectResponse
    {
        $this->resourceRepository->add($request);
        return response()->json([
            'result' => 'success',
        ], 200);

    }
    public  function update(Request $request,Category $category)
    {
        $this->resourceRepository->update($request,$category);
        return response()->json([
            'result' => 'success',
        ], 200);
    }
    public function show( $id)
    {
        $centerDetails= $this->resourceRepository->getCategory($id);

        return $this->respondSuccess(
            [
                'category' => $centerDetails
            ]
        );
    }
    public function destroy(Request $request, Category $category)
    {

        if (!is_null($category)) {
            $this->resourceRepository->delete($category);
            return response()->json([
                'result' => 'success',
                'message' => ''
            ], 200);
        }
        return response()->json([
            'result' => 'failed',
            'message' => 'category not found',
        ], 500);
    }
    public function allCategories()
    {


        $categories = $this->resourceRepository->getAllCategories();

        return $this->respondSuccess([
            'categories'=>$categories,
        ]);


    }

}
