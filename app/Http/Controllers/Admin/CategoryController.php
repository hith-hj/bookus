<?php

namespace App\Http\Controllers\Admin;
use App\Repositories\CategoryRepository;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use Illuminate\Http\Request;
//CategoryController
class CategoryController extends ApiController
{

    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
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
        $categories = $this->categoryRepository->getCategories($request)->paginate($limit);

        return $this->respondSuccess($categories->all(), $this->createApiPaginator($categories));


    }
    public function store(Request $request)//: RedirectResponse
    {
        $this->categoryRepository->add($request);
        return response()->json([
            'result' => 'success',
        ], 200);

    }
    public  function update(Request $request,Category $category)
    {
        $this->categoryRepository->update($request,$category);
        return response()->json([
            'result' => 'success',
        ], 200);
    }
    public function show( $id)
    {
    $centerDetails= $this->categoryRepository->getCategory($id);

        return $this->respondSuccess(
            [
                'category' => $centerDetails
            ]
        );
    }
    public function destroy(Request $request, Category $category)
    {

        if (!is_null($category)) {
            $this->categoryRepository->delete($category);
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

      
        $categories = $this->categoryRepository->getAllCategories();

        return $this->respondSuccess([
            'categories'=>$categories,
        ]);


    }

}
