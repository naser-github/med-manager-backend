<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\Setting\RoleListResource;
use App\Http\Services\setting\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index(RoleService $roleService): JsonResponse
    {
        $roleList = $roleService->index();

        return response()->json(['success' => true, 'roleList' => RoleListResource::collection($roleList)], 200);
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
