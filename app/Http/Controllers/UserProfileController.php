<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\Setting\ProfileResource;
use App\Http\Resources\Setting\UserDetailResource;
use App\Http\Services\ProfileService;
use App\Http\Services\setting\UserService;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{

    /**
     * @param ProfileService $profileService
     * @return JsonResponse
     */
    public function show(ProfileService $profileService): JsonResponse
    {
        $profileData = $profileService->userData(); // gets profile data

        return response()->json(['success' => true, 'userDetail' => new UserDetailResource($profileData)], 200);
    }

    /**
     * @param UpdateProfileRequest $request
     * @param UserService $userService
     * @param ProfileService $profileService
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request, UserService $userService, ProfileService $profileService): JsonResponse
    {

        $validatedData = $request->validated();

        $user = $userService->findByAuthId();
        $userProfile = $profileService->findByAuthId();

        if (!$user || !$userProfile)
            return response()->json(['success' => false, 'msg' => "User Doesn't exist"], 404);

        $user->name = $validatedData['name'];
        $user->save();

        $userProfile->user_phone = $validatedData['profile']['user_phone'];
        $userProfile->save();

        return response()->json(['success' => true, 'msg' => "Profile updated successfully"], 201);
    }

    public function updatePassword(UpdatePasswordRequest $request, UserService $userService)
    {
        $validatedData = $request->validated();

        $user = $userService->findByAuthId();

        if (!$user)
            return response()->json(['success' => false, 'msg' => "User Doesn't exist"], 404);

        if (Hash::check($validatedData['current_password'], $user->password)) {
            $user->password = Hash::make($validatedData['password']);
            $user->save();
        } else {
            return response()->json(['success' => false, 'msg' => "Wrong password"], 404);
        }

        return response()->json(['success' => true, 'msg' => "Profile changed successfully"], 201);
    }
}
