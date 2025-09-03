<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class appointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
            $oVal = new stdClass();
            // $oVal->extendedProps = "red"; // this creates a warning with PHP < 8
                                            // and a fatal error with PHP >=8
            // $oVal->key1->var2 = "something else";
            $xrand=rand(1,6);
            $color=['Business','Holiday','Family','Personal','ETC','Business','Holiday','Family','Personal','ETC'];
           
            $oVal->calendar=  $color[rand(1,9)];
        return [
            // 'id'=>$this->id,
            'title'=>$this->user->first_name,
            'start'=>"2023-09-22T19:41:09.421Z",
            'end' =>"2023-09-22T19:41:09.421Z",
            'extendedProps'=> $oVal
        ];
    }
}
