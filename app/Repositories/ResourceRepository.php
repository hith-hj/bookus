<?php

namespace App\Repositories;

use App\Models\Center;
use App\Models\Resource;
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

class  ResourceRepository
{

    public function add(Request $request)//: User
    {
        $resource = new Resource(populateModelData($request, Resource::class));

        if ($request->hasFile('image'))
            $resource->image = Storage::disk('public')->put('resourcees', $request->file('image'));
        if ($request->hasFile('image')) {
            // if there is an old image delete it
            if ($request->hasFile('image')) {
                // if there is an old cover_image delete it
                if ($resource->image != null) {
                    $resource->image = Storage::disk('public')->delete($resource->image);
                }
            }
            // store the new image
            $resource->image = Storage::disk('public')->put('resourcees', $request->file('image'));


        }
        $resource->save();
    }

    public  function getResources( Request $request){
    $resources=Resource::query();

        return $resources->latest();
    }
    public function getResource( $resource) {
        $resourceDetails=Resource::query()->findOrFail($resource);
        return $resourceDetails;
    }
    public function update(Request $request,$resource)  {
        $resource->update(populateModelData($request, Resource::class));
        if ($request->hasFile('image')) {
            // if there is an old image delete it
            if ($request->hasFile('image')) {
                // if there is an old cover_image delete it
                if ($resource->image != null) {
                    $resource->image = Storage::disk('public')->delete($resource->image);
                }
            }
            // store the new image
            $resource->image = Storage::disk('public')->put('resourcees', $request->file('image'));


        }
        $resource->save();
    }

    public function delete(Resource $resource)
    {
        $resource->delete();

    }
   public function getAllCategories()
   {
    return Resource::query()->get();
   }

}
