<?php

namespace App\Http\Resources\Setting\Role;

use App\Http\Resources\Setting\Permission\PermissionListResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleDetailResource extends JsonResource
{

    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        $permissions = $this->whenLoaded('permissions');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => PermissionListResource::collection($permissions),
        ];
    }
}
