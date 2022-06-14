<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function editProfile($id)
    {

        $user = User::where('users.id', $id)
            ->leftJoin('user_profiles', 'user_profiles.fk_user_id', '=', 'users.id')
            ->first();


        if (!$user) {
            $response = [
                'msg' => 'No User Found!!'
            ];
            return response($response, 404);
        }

        $response = [
            'user' => $user,
        ];
        return response($response, 200);
    }

    public function updateProfile(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'phone' => 'required|min:11||max:14',
            'password' => 'present|confirmed:password_confirm',
            'status' => 'required',
        ]);

        $user = User::where('id', $id)->first();

        if (!$user) {
            $response = [
                'msg' => "User Doesn't exist",
            ];
            return response($response, 404);
        }

        $name = $request->input('name');
        $phone = $request->input('phone');

        $user->name = $name;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $user_profile = UserProfile::where('fk_user_id', $id)->first();
        $user_profile->user_phone = $phone;
        $user_profile->save();

        $response = [
            'msg' => 'Profile details has been updated'
        ];
        return response($response, 201);
    }
}
