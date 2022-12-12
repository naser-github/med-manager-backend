<?php

namespace App\Http\Resources\Setting\Role;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleListResource extends JsonResource
{

    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
