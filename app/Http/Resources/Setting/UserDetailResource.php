<?php

namespace App\Http\Resources\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{

    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        $profile = $this->whenLoaded('profile');
        $roles = $this->whenLoaded('roles');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'user_status' => $this->user_status,
            'profile' => new ProfileResource($profile),
            'roles' => RoleListResource::collection($roles),
        ];
    }
}
