<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Http\Services\AuthService;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function channel($channel)
    {
        return Socialite::driver($channel)->redirect();
    }

    public function channelCallback($channel, AuthService $authService)
    {
        try {
            $user = Socialite::driver($channel)->stateless()->user();
        } catch (\Exception $error) {
            return redirect(env('CLIENT_BASE_URL') . '/auth/sign-in?error=Unable to login using ' . $channel . '. Please try again');
        }

        $userExist = $authService->findByEmail($user->email);

        $token = null;

        if (!$userExist) {

            $newUser = $authService->socialMediaLogin($user);
            $newUser->assignRole('user');
            $authService->setProfile($newUser->id, $user->avatar);
            $token = $newUser->createToken('my-app-token')->plainTextToken;
        } else
            $token = $userExist->createToken('my-app-token')->plainTextToken;

        return redirect(
            env('CLIENT_BASE_URL') . '/auth/social-callback?token=' . $token . '&name=' . $user->name . '&email=' . $user->email
        );
    }

    public function signIn(SignInRequest $request, AuthService $authService): JsonResponse
    {
        $validatedData = $request->validated();

        $user = $authService->findByEmail($validatedData['email']);

        if ($user && Hash::check($validatedData['password'], $user->password)) {
            if ($user->user_status != 'active')
                throw ValidationException::withMessages(['message' => [trans('auth.statusInactive')]]);
        } else throw ValidationException::withMessages(['message' => [trans('auth.signInFailed')]]);

        return response()->json([
            'success' => true,
            'data' => [
                'message' => [trans('auth.signInSuccess')],
                'token' => $user->createToken('my-app-token')->plainTextToken,
                'user' => new AuthResource($user)
            ]
        ], 200);
    }

    public function signUp(SignUpRequest $request, AuthService $authService): JsonResponse
    {
        $validatedData = $request->validated();

        $user = $authService->createUser($validatedData);
        $user->assignRole($validatedData['role']);
        $authService->setProfile($user->id, $validatedData['phone']);

        return response()->json([
            'success' => true,
            'message' => [trans('auth.signUpSuccess')],
        ], 201);
    }
}
