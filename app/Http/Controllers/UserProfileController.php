<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\Setting\ProfileResource;
use App\Http\Resources\Setting\UserDetailResource;
use App\Http\Services\ProfileService;
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

//    public function editProfile($id)
//    {
//        $profileData = $profileService->show();
//        $user = User::where('users.id', $id)
//            ->leftJoin('user_profiles', 'user_profiles.fk_user_id', '=', 'users.id')
//            ->first();
//
//
//        if (!$user) {
//            $response = [
//                'msg' => 'No User Found!!'
//            ];
//            return response($response, 404);
//        }
//
//        $response = [
//            'user' => $user,
//        ];
//        return response($response, 200);
//    }

    public function updateProfile(UpdateProfileRequest $request, ProfileService $profileService)
    {
        $profileData = $profileService->userData();

        if (!$profileData) {
            $response = ['msg' => "User Doesn't exist"]; return response($response, 404);
        }

        $user->save();

        $user_profile = UserProfile::where('fk_user_id', $id)->first();
        $user_profile->user_phone = $phone;
        $user_profile->save();

        $response = [
            'msg' => 'Profile details has been updated'
        ];
        return response($response, 201);
    }
}
