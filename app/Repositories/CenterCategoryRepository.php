<?php

namespace App\Repositories;

use App\Models\CenterCategory;
use App\Models\Center;
use App\Models\Contact;
use App\Models\Image;
use App\Models\OpenDay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class  CenterCategoryRepository
{

    public function add(Request $request)//: User
    {
        $category = new Category(populateModelData($request, Category::class));
        $category->save();
    }

    public  function getCategories( Request $request){
    $categories=CenterCategory::query();
    if ($search = $request->get('search')) {
        $categories->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');

        });}
        return $categories->latest();
    }
    public function getCategory( $category) {
        $categoryDetails=CenterCategory::query()->findOrFail($category);
        return $categoryDetails;
    }
    public function update(Request $request,$category)  {

        $category->update(populateModelData($request, CenterCategory::class));
        $category->save();
    }

   public function getAllCategories($center)
   {
    return CenterCategory::query()->where('center_id',$center->id)->get();
   }

}
