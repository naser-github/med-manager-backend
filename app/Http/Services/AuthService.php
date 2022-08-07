<?php

namespace App\Http\Services;


use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Expr\Cast\Object_;

class AuthService
{
    public function createUser($payload): Object
    {
        $user = new User();
        $user->name = $payload['name'];
        $user->email = $payload['email'];
        $user->password = Hash::make($payload['password']);
        $user->save();

        return $user;
    }

    public function findByEmail($payload): Object
    {
        return User::query()->where('email', $payload)->first();
    }

    public function setProfile($payload, $phoneNo)
    {
        $user_profile = new UserProfile();
        $user_profile->fk_user_id = $payload;
        $user_profile->user_phone = $phoneNo;
        $user_profile->save();
    }

    public function signIn($payload)
    {

    }
}
