<?php

namespace App\Http\Resources\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
{

    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        $profile = $this->whenLoaded('profile');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            "user_status" => $this->user_status,
            'profile' => new ProfileResource($profile),
        ];
    }
}
