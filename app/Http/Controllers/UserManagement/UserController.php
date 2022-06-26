<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:11||max:14',
            'password' => 'required',
            'role' => 'required'
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $user->assignRole($request->input('role'));

        $user_profile = new UserProfile();
        $user_profile->fk_user_id = $user->id;
        $user_profile->user_phone = $request->input('phone');
        $user_profile->save();

        $response = [
            'msg' => 'User has been created'
        ];

        return response($response, 201);
    }
}
