<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::all();

        return view('pages.admin.role_management.index',
            compact('roles'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|unique:roles',
        ]);

        $role = new Role();
        $role->name = $request->name;
        $role->save();

        Session::flash('success', 'Role has been created');
        return Redirect::route('roles.index');
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request)
    {
        $id = $request->id;

        $role = Role::where('id', $id)->first();

        if ($role)
            return view('pages.admin.role_management.edit',
                compact('role'));
        else
            return back();
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required|unique:roles',
        ]);

        $role = Role::where('id', $id)->first();

        if ($role) {
            $role->name = $request->name;
            $role->save();
            Session::flash('success', 'Role name has been updated');
            return Redirect::route('roles.index');
        } else
            return back();
    }

    public function destroy($id)
    {
        //
    }
}
