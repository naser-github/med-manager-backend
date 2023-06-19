<?php

namespace App\Http\Resources\Setting\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionDetailResource extends JsonResource
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
