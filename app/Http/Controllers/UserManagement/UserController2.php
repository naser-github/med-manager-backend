<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UserController2 extends Controller
{

    public function edit($id)
    {
        $user = User::where('id', $id)->first();

        if ($user) {
            $roles = DB::table("roles")->select('id', 'name')->get();
        } else

        return back();
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required|regex:" " ',
            'phone' => 'required|min:11||max:14',
            'password' => 'present|confirmed:password_confirm',
        ]);

        $user = User::where('id', $id)->first();

        if (!$user) {
            return back();
        }

        $user->name = $request->input('name');
        $user->user_status = $request->input('status');
        if ($request->password) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        $user->syncRoles($request->input('role'));

        $user_profile = UserProfile::where('fk_user_id', $id)->first();
        $user_profile->user_phone = $request->input('phone');
        $user_profile->save();

    }

    public function show($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
