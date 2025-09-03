<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use App\Repositories\AddressRepository;
use Illuminate\Http\Request;

class AddressController extends ApiController
{
    private $addressRepository;
    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }
    
    
    public function index(Request $request)
    {
        $user=User::find(auth('client')->id());
        $limit = $request->get('limit') ?: 10;
        if ($limit > 30) $limit = 30;
        $addresses = $this->addressRepository->getAddresses($request,$user)->paginate($limit);

        return $this->respondSuccess($addresses->all(), $this->createApiPaginator($addresses));
    }


    public function store(Request $request)//: RedirectResponse
    {
        $user=User::find(auth('client')->id());
        $this->addressRepository->add($request,$user);
        return response()->json([
            'result' => 'Address Created',
        ], 200);

    }
    
    
    public function destroy(Request $request, Address $address)
    {
        if (!is_null($address)) {
            $this->addressRepository->delete($address);
            return response()->json([
                'result' => 'Address Deleted',
            ], 200);
        }
        return response()->json([
            'result' => 'failed',
            'message' => 'center not found',
        ], 500);
    }
    

    public function update(Request $request, Address $address)
    {
        $this->addressRepository->update($request, $address);
        return response()->json([
            'result' => 'Address updated',
        ], 200);
    }
    

    public function setDefault(Request $request)
    {
        $user=User::find(auth('client')->id());
        $id=$request->get('id');
        $addresses=Address::where('user_id',$user->id)->get();
        foreach ($addresses as $adrs)
        {
            $adrs->default = $adrs->id == $id ? 1 : 0;
            $adrs->save();
        }
        return $this->respondSuccess();
    }
}
