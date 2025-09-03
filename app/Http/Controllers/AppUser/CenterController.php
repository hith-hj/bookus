<?php

namespace App\Http\Controllers\AppUser;
use App\Http\Controllers\Admin\Application;
use App\Http\Controllers\Admin\Factory;
use App\Http\Controllers\Admin\View;
use App\Models\Admin;
use App\Models\Center;
use App\Models\Contact;
use App\Models\Favorite;
use App\Models\Image;
use App\Models\OpenDay;
use App\Models\Review;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Repositories\CenterRepository;
use App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
//CategoryController
class CenterController extends ApiController
{
    private $centerRepository;

    public function __construct(CenterRepository $centerRepository)
    {
        $this->centerRepository = $centerRepository;
    }

    public function index(Request $request)
    {
        $limit = $request->filled('limit') && $request->limit < 30 ? $request->limit : 30;
        $centers = $this->centerRepository->getcenters($request)->paginate($limit);
        return $this->respondSuccess($centers->all(), $this->createApiPaginator($centers));
    }
    
    public  function update(Request $request,Center $center)
    {
        $this->centerRepository->update($request,$center);
        return response()->json(['result' => 'success',], 200);
    }
    
    public function show( $id)
    {
        $centerDetails= $this->centerRepository->getCenter($id);
        return $this->respondSuccess(['center' => $centerDetails]);
    }
    
    public function destroy(Request $request, Center $center)
    {
        // if (!is_null($center)) {
        //     $this->centerRepository->delete($center);
        //     return response()->json([
        //         'result' => 'success',
        //         'message' => ''
        //     ], 200);
        // }
        return response()->json([
            'result' => 'failed',
            'message' => 'center not found',
        ], 500);
    }
    
    public function getCenterDetails(Request $request)
    {
        $user =User::findOrFail(auth('client')->id());
        $center=Center::with(['resources'])->findOrFail($request->center_id);
        $center->is_favorite=Favorite::where([['user_id',$user->id],['center_id',$center->id]])->exists();
        $center->makeHidden(['centerServices']);
        foreach($center->contacts as $contact){
            $contact->image = file_exists('social/'.lcfirst($contact->key).'.png')?'social/'.lcfirst($contact->key).'.png':null;
        }
        return $this->respondSuccess($center);
    }
}
