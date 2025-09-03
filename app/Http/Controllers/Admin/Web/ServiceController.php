<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Admin\Web\AdminBaseController as Controller;
use App\Models\Category;
use App\Models\CenterService;
use App\Repositories\CenterServiceRepository;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    private $repo;

    public function __construct(CenterServiceRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request){
        $services = CenterService::with(['center:id,name'])
        ->when($request->filled('search'),function($query) use($request){
            return $query->where([['name','like','%'.$request->search.'%']]);
        })->when($request->filled('filter'),function($query) use($request){
            if(in_array($request->filter,['Males','Females'])){
                return $query->where('service_gender',$request->filter);
            }                
        })->paginate(20);
        return view('admin.pages.services',['services'=>$services]);
    }

    public function store(Request $request){
        $data = $request->validate([
            'name'=>['required','string','unique:categories,name'],
            'image'=>['required','image'],
        ]);
        try{
            $this->repo->add($request);
            return redirect()->back()->with('success','Category Created');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }
    }

    public function delete(Request $request , $id){
        $category = Category::findOrFail($id);
        try{
            $this->repo->delete($category);
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
