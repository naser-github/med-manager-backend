<?php

namespace App\Http\Resources\Prescription;

use App\Http\Resources\Dose\DoseResource;
use App\Http\Resources\Medicine\MedicineResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescribedMedicineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $dose = $this->whenLoaded('dose');
        $medicine = $this->whenLoaded('medicine');
        return [
            'id' => $this->id,
            'medicine' => new MedicineResource($medicine),
            'dose' => DoseResource::collection($dose),
            "status" => $this->status,
            "time_period" => $this->time_period,
        ];
    }
}
