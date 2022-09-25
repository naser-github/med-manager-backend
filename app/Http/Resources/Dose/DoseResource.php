<?php

namespace App\Http\Resources\Dose;

use Illuminate\Http\Resources\Json\JsonResource;

class DoseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            "label" => $this->label,
            "time" => $this->time,
            "status" => $this->status,
        ];
    }
}
