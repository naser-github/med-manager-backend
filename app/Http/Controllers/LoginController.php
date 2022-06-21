<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    function signIn(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password) || $user->status == 'inactive') {
            return response([
                'message' => ['These credentials do not match any records.']
            ], 401);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;
        unset($user->id);

        $response = [
            'user' => $user,
            'token' => $token
        ];


        return response($response, 200);
    }
}
