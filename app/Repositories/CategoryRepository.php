<?php

namespace App\Repositories;

use App\Models\Category;
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

class  CategoryRepository
{

    public function add(Request $request)//: User
    {
        $category = new Category(populateModelData($request, Category::class));

        if ($request->hasFile('image'))
            $category->image = Storage::disk('public')->put('categories', $request->file('image'));
        if ($request->hasFile('image')) {
            // if there is an old image delete it
            if ($request->hasFile('image')) {
                // if there is an old cover_image delete it
                if ($category->image != null) {
                    $category->image = Storage::disk('public')->delete($category->image);
                }
            }
            // store the new image
            $category->image = Storage::disk('public')->put('categories', $request->file('image'));


        }
        $category->save();
    }

    public  function getCategories( Request $request){
    $categories=Category::query();
    if ($search = $request->get('search')) {
        $categories->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');

        });}
        return $categories->latest();
    }
    public function getCategory( $category) {
        $categoryDetails=Category::query()->findOrFail($category);
        return $categoryDetails;
    }
    public function update(Request $request,$category)  {
        $category->update(populateModelData($request, Category::class));
        if ($request->hasFile('image')) {
            // if there is an old image delete it
            if ($request->hasFile('image')) {
                // if there is an old cover_image delete it
                if ($category->image != null) {
                    $category->image = Storage::disk('public')->delete($category->image);
                }
            }
            // store the new image
            $category->image = Storage::disk('public')->put('categories', $request->file('image'));


        }
        $category->save();
    }

    public function delete(Category $category)
    {
        if ($category->image != null) {
            $category->image = Storage::disk('public')->delete($category->image);
        }
        $category->delete();
    }
   public function getAllCategories()
   {
    return Category::query()->get();
   }

}
