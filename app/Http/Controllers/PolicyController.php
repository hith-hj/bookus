<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class PolicyController extends ApiController
{
    public function polices(Request $request)
    {
        $policy=Policy::query()->get();
        return $this->respondSuccess($policy);
    }
}
