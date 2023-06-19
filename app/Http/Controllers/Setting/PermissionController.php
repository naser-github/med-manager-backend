<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\Permission\PermissionStoreReuqest;
use App\Http\Requests\Setting\Permission\PermissionUpdateReuqest;
use App\Http\Resources\Setting\Permission\PermissionDetailResource;
use App\Http\Resources\Setting\Permission\PermissionListResource;
use App\Http\Services\setting\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * @param PermissionService $permissionService
     * @return JsonResponse
     */
    public function index(PermissionService $permissionService): JsonResponse
    {
        $permissionList = $permissionService->index();

        return response()->json(['success' => true, 'permissionList' => PermissionListResource::collection($permissionList)], 200);
    }

    /**
     * @param PermissionStoreReuqest $request
     * @param PermissionService $permissionService
     * @return JsonResponse
     */
    public function store(PermissionStoreReuqest $request, PermissionService $permissionService): JsonResponse
    {
        $validateData = $request->validated(); // validating data

        $permissionService->store($validateData['name']); // sending validated data to service

        return response()->json(['success' => true, 'message' => 'permission created successfully'], 201);
    }


    /**
     * @param $id
     * @param PermissionService $permissionService
     * @return JsonResponse
     */
    public function edit($id, PermissionService $permissionService): JsonResponse
    {
        $permission = $permissionService->findById($id);

        if ($permission === null)
            return response()->json(['success' => false, 'message' => 'permission not found'], 404);
        else
            return response()->json(['success' => true, 'permission' => new PermissionDetailResource($permission)], 200);
    }

    /**
     * @param $id
     * @param PermissionUpdateReuqest $request
     * @param PermissionService $permissionService
     * @return JsonResponse
     */
    public function update($id, PermissionUpdateReuqest $request, PermissionService $permissionService): JsonResponse
    {
        $validateData = $request->validated(); // validating data

        $permission = $permissionService->findById($id); // checking if role exist

        if ($permission === null)
            return response()->json(['success' => false, 'message' => 'permission not found'], 404);
        else {
            $permissionService->update($permission, $validateData['name']);
            return response()->json(['success' => true, 'message' => 'permission updated successfully'], 201);
        }
    }

    public function destroy($id)
    {
        //
    }
}
