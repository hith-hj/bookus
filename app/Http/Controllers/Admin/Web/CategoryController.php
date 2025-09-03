<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use App\Models\Category;
use App\Models\Center;
use App\Models\CenterCategory;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(Request $request){
        $categories = Category::when($request->has('search'),function($query) use($request){
            $query->where([['name','like','%'.$request->search.'%']]);
        })->when($request->filled('filter'),function($query) use ($request){
            if(in_array($request->filter,['active','inactive'])){
                if($request->filter == 'active'){
                    $query->where('status',1);    
                }else{
                    $query->where('status',0);    
                }
            }
        })->get();
        foreach($categories as $category){
            $category = $this->setCategoryCenters($category);
        }
        return view('admin.pages.categories',['categories'=>$categories]);
    }

    private function setCategoryCenters($category){
        $ids = CenterCategory::where('name',$category->name)->pluck('center_id');
        return $category->centers = Center::find($ids,['id','name']);
    }

    public function store(Request $request){
        $data = $request->validate([
            'name'=>['required','string','unique:categories,name'],
            'image'=>['required','image'],
        ]);
        try{
            $this->categoryRepository->add($request);
            return redirect()->back()->with('success','Category Created');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }
    }

    public function delete(Request $request , $id){
        $category = Category::findOrFail($id);
        try{
            $this->categoryRepository->delete($category);
            return redirect()->back()->with('success','Category deleted');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }
    }

    public function toggleCategory(Request $request , $id){
        $category = Category::findOrFail($id);
        try{
            $category->update(['status'=>!$category->status]);
            return redirect()->back()->with('success','Category updated');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }
    }
}
