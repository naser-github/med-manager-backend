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

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();

        return view('pages.admin.user_management.index',
            compact('users'));
    }

    public function create()
    {
        $roles = DB::table("roles")->select('id', 'name')->get();

        return view('pages.admin.user_management.create',
            compact('roles'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|regex:" " ',
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:11||max:14',
            'password' => 'required|confirmed:password_confirm',
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->user_status = 'active';
        $user->save();

        $user->assignRole($request->input('role'));

        $user_profile = new UserProfile();
        $user_profile->fk_user_id = $user->id;
        $user_profile->user_phone = $request->input('phone');
        $user_profile->save();

        Session::flash('success', 'User has been created');
        return Redirect::route('users.index');
    }

    public function edit($id)
    {
        $user = User::where('id', $id)->first();

        if ($user) {
            $roles = DB::table("roles")->select('id', 'name')->get();
            return view('pages.admin.user_management.edit',
                compact('user', 'roles'));
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

        Session::flash('success', 'User details has been updated');
        return Redirect::route('users.index');
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
