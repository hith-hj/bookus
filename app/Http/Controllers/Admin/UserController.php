<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Requests\UserRequest;

use App\Repositories\UserRepository;
use App\Models\User;

use App\Http\Controllers\ApiController;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use PDF;

class UserController extends ApiController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit') ?: 10;
        if ($limit > 30) $limit = 30;
        $users = $this->userRepository
            ->getUsers($request)
            ->paginate($limit);

        return $this->respondSuccess($users->all(), $this->createApiPaginator($users));
    }

    public function allUser(Request $request)
    {
        $currencies = User::query()->pluck('name');
        return $currencies;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $this->userRepository->add($request);

        return response()->json([
            'result' => 'success',
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=User::find($id);
        return $this->respondSuccess(
            [
                'user' => $user
            ]
        );

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->userRepository->update($request, $user);
        return response()->json([
            'result' => 'success',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {

        if (!is_null($user)) {
            $this->userRepository->delete($user);
            return response()->json([
                'result' => 'success',
                'message' => ''
            ], 200);
        }
        return response()->json([
            'result' => 'failed',
            'message' => 'user not found',
        ], 500);
    }
    public function  userExportExcel(Request $request){
        Excel::store(new UsersExport($request->search),'userExcel/clients.xlsx','public',\Maatwebsite\Excel\Excel::XLSX);
        return response()->download(public_path('storage/userExcel/clients.xlsx'));


    }
    public function userDownloadPDF(Request $request)
    {
        $users = User::query();
        $search =$request->search;
        if ($search) {
            $users->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        $users=  $users->latest()->get();
        $pdf = PDF::loadView('pdf.userPdf',array('users' =>  $users))->save('storage/userPdf/users.pdf');
        return response()->download(public_path('storage/userPdf/users.pdf'));
    }


}
