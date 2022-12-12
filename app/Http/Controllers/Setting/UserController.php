<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\User\UserStoreRequest;
use App\Http\Requests\Setting\User\UserUpdateRequest;
use App\Http\Resources\Setting\RoleListResource;
use App\Http\Resources\Setting\UserDetailResource;
use App\Http\Resources\Setting\UserListResource;
use App\Http\Services\setting\RoleService;
use App\Http\Services\setting\UserService;
use App\Http\Traits\HelperFunctionTrait;
use App\Jobs\SendNewUserEmailJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use HelperFunctionTrait;

//    public function __construct()
//    {
//        $this->middleware('permission:user_read', ['only' => ['index']]);
//
//        $this->middleware('permission:user_write', ['only' => [
//            'store', 'edit', 'update'
//        ]]);
//    }

    /**
     * @param UserService $userService
     * @return JsonResponse
     */
    public function index(UserService $userService): JsonResponse
    {
        $userList = $userService->index(); // show list of all user which is in order by name

        return response()->json(['success' => true, 'userList' => UserListResource::collection($userList)], 200);
    }

    /**
     * @param UserStoreRequest $request
     * @param UserService $userService
     * @return JsonResponse
     */
    public function store(UserStoreRequest $request, UserService $userService): JsonResponse
    {
        $validateData = $request->validated();

        DB::beginTransaction();
        try {
            $password = $this->passwordGenerator(8); // calling helper function to generate a random password

            $user = $userService->store($validateData['formData'], $password); // storing user data on the user table

            $userService->storeProfile($user->id, $validateData['formData']['phone']); // storing user profile data

            SendNewUserEmailJob::dispatch($user); // sending mail to newly created user with their new password

            DB::commit();
            return response()->json(['success' => true, 'message' => 'User has been created successfully'], 201);
        } catch (\Exception $error) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'User creation failed' . $error,], 500);
        }
    }

    /**
     * @param $id
     * @param RoleService $roleService
     * @param UserService $userService
     * @return JsonResponse
     */
    public function edit($id, RoleService $roleService, UserService $userService): JsonResponse
    {
        $userData = $userService->userData($id); // search for user

        if (!$userData) return response()->json([
            'success' => false, 'message' => 'user not found'
        ], 404);

        $roleList = $roleService->index(); // get role list

        return response()->json([
            'success' => true,
            'roleList' => RoleListResource::collection($roleList),
            'userData' => new UserDetailResource($userData)
        ], 200);
    }

    /**
     * @param $id
     * @param UserUpdateRequest $request
     * @param UserService $userService
     * @return JsonResponse
     */
    public function update($id, UserUpdateRequest $request, UserService $userService): JsonResponse
    {
        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
            $user = $userService->findById($validatedData['id']); // find userdata

            $userService->update($user, $validatedData); // update user data

            $userService->updateProfile($user->id, $validatedData['profile']['user_phone']); // update profile data

            $user->roles()->sync([$validatedData['roles'][0]['id']]); // updating user role

            DB::commit();
            return response()->json(['success' => true, 'message' => 'User has been successfully updated'], 201);
        } catch (\Exception $error) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'User update failed' . $error,], 500);
        }
    }
}
