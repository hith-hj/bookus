<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CenterRequest;
use App\Http\Requests\AdminRequest;
use App\Models\Center;
use App\Models\Contact;
use App\Repositories\CenterRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\CentersExport;


class CenterController extends ApiController
{
    private $centerRepository;

    public function __construct(CenterRepository $centerRepository)
    {
        $this->centerRepository = $centerRepository;
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
        $centers = $this->centerRepository->getCenters($request)->paginate($limit);

        return $this->respondSuccess($centers->all(), $this->createApiPaginator($centers));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param CenterRequest $request
     * @return RedirectResponse
     */
    public function store(Request $request) //: RedirectResponse
    {
        $this->centerRepository->add($request);
        return response()->json([
            'result' => 'success',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param Center $center
     * @return Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $centerDetails = $this->centerRepository->getCenter($id);
        return $this->respondSuccess(['center' => $centerDetails]);
    }


    public function update(Request $request, Center $center)
    {
        $this->centerRepository->update($request, $center);
        return $this->respondSuccess(['message' => 'success']);
    }

    public function addContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'value' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $contact = new Contact();
        $contact->key = $request->key;
        $contact->value = $request->value;
        $contact->center()->associate($request->id);
        $contact->save();
        return $this->respondSuccess(['message' => 'contact Added']);
    }

    public function destroy(Request $request, Center $center)
    {

        if (!is_null($center)) {
            $this->centerRepository->delete($center);
            // return response()->json([
            //     'result' => 'success',
            //     'message' => ''
            // ], 200);
            return $this->respondSuccess(['message' => 'center Deleted']);
        }
        return $this->respondMessage('Error', ['message' => 'center Not found']);

        // return response()->json([
        //     'result' => 'failed',
        //     'message' => 'center not found',
        // ], 500);
    }
    public function centerDownloadPDF(Request $request)
    {
        $centers = Center::query();
        $search = $request->search;
        if ($search) {
            $centers->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('currency', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        $centers =  $centers->latest()->get();
        $pdf = PDF::loadView('pdf.centerPdf', array('centers' =>  $centers))->save('storage/centerPdf/centers.pdf');
        return response()->download(public_path('storage/centerPdf/centers.pdf'));
    }
    // centerExportExcel
    public function centerExportExcel(Request $request)
    {
        Excel::store(new CentersExport($request->search), 'centerExcel/centers.xlsx', 'public', \Maatwebsite\Excel\Excel::XLSX);
        return response()->download(public_path('storage/centerExcel/centers.xlsx'));
    }
    // CENTER METHODS
    //member methods
    public function getMembers()
    {
        $id = $this->getCenter();
        $center = Center::with('admins')->find($id);
        return $this->respondSuccess($center);
    }
    public function newMember(AdminRequest $request)
    {

        $member = $this->centerRepository->newMember($request);
        $member->assignRole('Center');
        return $this->respondSuccess();
    }
    // center contacts 
    public function  getContact()
    {
        $id = $this->getCenter();
        $center = Center::with('contacts')->find($id);
        return $this->respondSuccess($center);
    }
    //add new contact
    public function newContact(Request $request)
    {

        $member = $this->centerRepository->newContact($request);
        return $this->respondSuccess();
    }
    public  function delContact($id)
    {
        $contactStatus = Contact::query()->find($id)->delete();
        return $this->respondSuccess();
    }
    public function updateCenterDays(Request $request)
    {
        $id = $this->getCenter();
        $center = Center::with('days')->find($id);
        $center = $this->centerRepository->updateDays($request, $center);
        return $this->respondSuccess();
    }
    public function getCenterDetails()
    {
        $id = $this->getCenter();

        $centerDetails =    $this->centerRepository->getCenter($id);
        return $this->respondSuccess(
            [
                'center' => $centerDetails
            ]
        );
    }

    
}
