<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends ApiController
{

    public function faqs(Request $request)
    {
        $faqs =Faq::query()->get();
        return $this->respondSuccess($faqs);
    }
}
