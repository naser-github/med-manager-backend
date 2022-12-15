<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\Role\RoleStoreRequest;
use App\Http\Requests\Setting\Role\RoleUpdateRequest;
use App\Http\Resources\Setting\Permission\PermissionListResource;
use App\Http\Resources\Setting\Role\RoleDetailResource;
use App\Http\Resources\Setting\Role\RoleListResource;
use App\Http\Services\setting\PermissionService;
use App\Http\Services\setting\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

        DB::beginTransaction();
        try {
            $role = $roleService->store($validateData); // sending validated data to service
            $role->syncPermissions($validateData['permissions']); // syncing all the permissions to role
            DB::commit();
            return response()->json(['success' => true, 'message' => 'role created successfully'], 201);
        } catch (\Exception $error) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'failed to create role. ' . $error,], 404);
        }
    }


    /**
     * @param $id
     * @param PermissionService $permissionService
     * @param RoleService $roleService
     * @return JsonResponse
     */
    public function edit($id, PermissionService $permissionService, RoleService $roleService): JsonResponse
    {
        $role = $roleService->findByIdWithPermissions($id);

        if ($role === null)
            return response()->json(['success' => false, 'message' => 'role not found'], 404);
        else {
            $permissions = $permissionService->index();
            return response()->json([
                'success' => true,
                'permissions' => PermissionListResource::collection($permissions),
                'role' => new RoleDetailResource($role),
            ], 200);
        }
    }

    /**
     * @param $id
     * @param RoleUpdateRequest $request
     * @param RoleService $roleService
     * @return JsonResponse
     */
    public function update($id, RoleUpdateRequest $request, RoleService $roleService): JsonResponse
    {
        $validateData = $request->validated(); // validating data

        $role = $roleService->findById($id); // checking if role exist

        if ($role === null)
            return response()->json(['success' => false, 'message' => 'role not found'], 404);
        else {
            $roleService->update($role, $validateData['name']);
            $role->syncPermissions($validateData['permissions']);
            return response()->json(['success' => true, 'message' => 'role updated successfully'], 201);
        }
    }

    public function destroy($id)
    {
        //
    }
}
