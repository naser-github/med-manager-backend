<?php

namespace App\Http\Services;


use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Expr\Cast\Object_;

class AuthService
{
    /**
     * @param $payload
     * @return User
     */
    public function createUser($payload): User
    {
        $user = new User();
        $user->name = $payload['name'];
        $user->email = $payload['email'];
        $user->password = Hash::make($payload['password']);
        $user->save();

        return $user;
    }

    /**
     * @param $payload
     * @return Model|Builder|null
     */
    public function findByEmail($payload): Model|Builder|null
    {
        return User::query()->where('email', $payload)->first();
    }

    /**
     * @param $userId
     * @param $avatar
     * @param $phoneNo
     * @return void
     */
    public function setProfile($userId, $avatar=null, $phoneNo=null): void
    {
        $user_profile = new UserProfile();
        $user_profile->fk_user_id = $userId;
        $user_profile->avatar = $avatar;
        $user_profile->user_phone = $phoneNo;
        $user_profile->save();
    }

    /**
     * @param $payload
     * @return User
     */
    public function socialMediaLogin($payload): User
    {
        $user = new User();
        $user->name = $payload->name;
        $user->email = $payload->email;
        $user->save();

        return $user;
    }

}
