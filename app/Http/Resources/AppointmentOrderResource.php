<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CenterService;

class AppointmentOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'center_id'=>$this->center_id,
            'member_id'=>$this->member_id,
            'status'=>$this->status,
            'total'=>$this->total,
            'total_time'=>$this->total_time,
            'shift_start'=>$this->shift_start,
            'shift_end'=>$this->shift_end,
            'appointment_date'=>$this->appointment_date->format('Y-m-d'),
            'using_by'=>$this->using_by,
            'usingBy_id'=>$this->usingBy_id,
            'center_name'=>$this->center->name,
            'latitude'=>$this->center->latitude,
            'longitude'=>$this->center->longitude,
            'logo'=>$this->center->logo,
            'created_at'=>$this->created_at->format('Y-m-d'),
            'note'=>$this->note,
            'services'=>$this->appointmentServices,
            'ref'   =>$this->ref
        ];
    }
}
