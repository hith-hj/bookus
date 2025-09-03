<?php

namespace App\Http\Controllers\AppUser;
use App\Http\Controllers\Admin\Application;
use App\Http\Controllers\Admin\Factory;
use App\Http\Controllers\Admin\View;
use App\Models\Center;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Image;
use App\Models\OpenDay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\ApiController;
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

    public function allCategories(Request $request)
    {

        $limit = $request->get('limit') ?: 10;
        if ($limit > 30) $limit = 30;
        $categories = $this->categoryRepository->getCategories($request)->paginate($limit);

        return $this->respondSuccess($categories->all(), $this->createApiPaginator($categories));
    }

    public function centersByCategory(Request $request)
    {
        $limit = $request->filled('limit') && $request->limit < 30 ? $request->limit : 30;
        $category =Category::find($request->get('category_id'));
        if(!$category){
            return $this->respondError("Category Not found");
        }
        $centers=Center::query()
        ->with('reviews')
        ->whereHas('categories', function ($query) use ($category) {
            $query->where('name',$category->name);
        })
        ->when($request->filled('max_price') || $request->filled('serv_type') ,
            function($query) use ($request){
                $query->whereHas('services',function($subQuery) use ($request){
                    if($request->filled('max_price')){
                        $subQuery->where('retail_price', '<', $request->max_price);
                    }
                    if($request->filled('serv_type')){
                        $subQuery->where('service_gender', $request->serv_type);
                    }
                });
        })->get();
        if($request->filled('sort')){
            $centers = match($request->sort){
                'recommended'=>$centers->sortBy('rated'),
                'nearest'=>$centers->sortBy('user_dietance'),
                default => $centers->sortBy('user_dietance'),
            };
        }
        $centers->makeHidden(['phone_number',
            'status',
            'latitude',
            'city',
            'country',
            'longitude',
            'currency',
            'address',
            'email',
            'about',]);
        return $this->respondSuccess($centers);
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

}
