<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\Role\RoleStoreRequest;
use App\Http\Requests\Setting\Role\RoleUpdateRequest;
use App\Http\Resources\Setting\RoleDetailResource;
use App\Http\Resources\Setting\RoleListResource;
use App\Http\Services\setting\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    /**
     * @param RoleService $roleService
     * @return JsonResponse
     */
    public function index(RoleService $roleService): JsonResponse
    {
        $roleList = $roleService->index();

        return response()->json(['success' => true, 'roleList' => RoleListResource::collection($roleList)], 200);
    }

    /**
     * @param RoleStoreRequest $request
     * @param RoleService $roleService
     * @return JsonResponse
     */
    public function store(RoleStoreRequest $request, RoleService $roleService): JsonResponse
    {
        $validateData = $request->validated(); // validating data

        $roleService->store($validateData['name']); // sending validated data to service

        return response()->json(['success' => true, 'message' => 'role created successfully'], 201);
    }


    /**
     * @param $id
     * @param RoleService $roleService
     * @return JsonResponse
     */
    public function edit($id, RoleService $roleService): JsonResponse
    {
        $role = $roleService->findById($id);

        if ($role === null)
            return response()->json(['success' => false, 'message' => 'role no found'], 404);
        else
            return response()->json(['success' => true, 'role' => new RoleDetailResource($role)], 200);

    }

    /**
     * @param $id
     * @param RoleUpdateRequest $request
     * @param RoleService $roleService
     * @return JsonResponse
     */
    public function update($id, RoleUpdateRequest $request, RoleService $roleService)
    {
        $validateData = $request->validated(); // validating data

        $role = $roleService->findById($id); // checking if role exist

        if ($role === null)
            return response()->json(['success' => false, 'message' => 'role not found'], 404);
        else {
            $roleService->update($role, $validateData['name']);
            return response()->json(['success' => true, 'message' => 'role updated successfully'], 201);
        }
    }

    public function destroy($id)
    {
        //
    }
}
