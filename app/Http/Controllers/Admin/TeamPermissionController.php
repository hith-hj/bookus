<?php

namespace App\Http\Controllers\Admin;
use App\Repositories\TeamPermissionRepository;

use App\Http\Controllers\ApiController;
use App\Models\TeamPermission;
use Illuminate\Http\Request;
//TeamPermissionController
class TeamPermissionController extends ApiController
{

    private $teamPermissionRepository;

    public function __construct(TeamPermissionRepository $teamPermissionRepository)
    {
        $this->teamPermissionRepository = $teamPermissionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $permissions=TeamPermission::query()->pluck('name')->toArray();
        return $this->respondSuccess($permissions);


    }
    public function store(Request $request)//: RedirectResponse
    {
        $this->teamPermissionRepository->add($request);
        return response()->json([
            'result' => 'success',
        ], 200);

    }
    public  function update(Request $request,TeamPermission $teamPermission)
    {
        $this->teamPermissionRepository->update($request,$teamPermission);
        return response()->json([
            'result' => 'success',
        ], 200);
    }
    public function show( $id)
    {
    $centerDetails= $this->teamPermissionRepository->getTeamPermission($id);

        return $this->respondSuccess(
            [
                'teamPermission' => $centerDetails
            ]
        );
    }
    public function destroy(Request $request, TeamPermission $teamPermission)
    {

        if (!is_null($teamPermission)) {
            $this->teamPermissionRepository->delete($teamPermission);
            return response()->json([
                'result' => 'success',
                'message' => ''
            ], 200);
        }
        return response()->json([
            'result' => 'failed',
            'message' => 'teamPermission not found',
        ], 500);
    }
    public function allCategories()
    {


        $categories = $this->teamPermissionRepository->getAllCategories();

        return $this->respondSuccess([
            'categories'=>$categories,
        ]);


    }

}
