<?php

namespace App\Http\Services;


use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Expr\Cast\Object_;

class AuthService
{
    public function createUser($payload): object
    {
        $user = new User();
        $user->name = $payload['name'];
        $user->email = $payload['email'];
        $user->password = Hash::make($payload['password']);
        $user->save();

        return $user;
    }

    public function findByEmail($payload): object | null
    {
        return User::query()->where('email', $payload)->first();
    }

    public function setProfile($userId, $avatar=null, $phoneNo=null): void
    {
        $user_profile = new UserProfile();
        $user_profile->fk_user_id = $userId;
        $user_profile->avatar = $avatar;
        $user_profile->user_phone = $phoneNo;
        $user_profile->save();
    }

    public function socialMediaLogin($payload) : object
    {
        $user = new User();
        $user->name = $payload->name;
        $user->email = $payload->email;
        $user->save();

        return $user;
    }

}
