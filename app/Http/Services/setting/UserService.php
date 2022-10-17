<?php

namespace App\Http\Services\setting;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserService
{
    /**
     * @return Collection|array
     */
    public function index(): Collection|array
    {
        return User::query()->with(['profile'])->orderBy('name', 'ASC')->get();
    }


    /**
     * @param $payload
     * @return object|null
     */
    public function findById($payload): object|null
    {
        return User::query()->where('id', $payload)->first();
    }

    /**
     * @param $payload
     * @param $password
     * @return User
     */
    public function store($payload, $password): User
    {
        $user = new User();
        $user->name = $payload['name'];
        $user->password = $password;
        $user->email = $payload['email'];
        $user->save();

        $user->roles()->sync([$payload['role']]); // syncing user with a role

        return $user;
    }

    /**
     * @param $userId
     * @param $userPhone
     * @return void
     */
    public function storeProfile($userId, $userPhone): void
    {
        $profile = new UserProfile();
        $profile->fk_user_id = $userId;
        $profile->user_phone = $userPhone;
        $profile->save();
    }

    /**
     * @param $user
     * @param $payload
     * @return void
     */
    public function update($user, $payload): void
    {
        $user->name = $payload['name'];
        $user->email = $payload['email'];
        $user->user_status = $payload['user_status'];
        $user->save();
    }


    public function updateProfile($userId, $payload): void
    {
        $profile = UserProfile::query()->where('fk_user_id', $userId)->first();

        $profile->user_phone = $payload;
        $profile->save();
    }

    /**
     * @param $payload
     * @return object|null
     */
    public function userData($payload): object|null
    {
        return User::query()->with(['profile', 'roles'])->where('id', $payload)->first();
    }

}
