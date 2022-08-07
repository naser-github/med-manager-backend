<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Services\AuthService;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
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

    public function signIn(SignInRequest $request, AuthService $authService): JsonResponse
    {
        // $request->ensureIsNotRateLimited(); // check user login attempts

        $message = '';
        $token = '';
        $validatedData = $request->validated();

        $user = $authService->findByEmail($validatedData['email']);

        if ($user && Hash::check($validatedData['password'], $user->password)) {
            if ($user->status == 'active') $token = $user->createToken('my-app-token')->plainTextToken;
            else $message = [trans('auth.statusInactive')];
        } else $message = [trans('auth.signInFailed')];

        return response()->json([
            'success' => true,
            'message' => 'Welcome fellow mate',
            'response' => [
                'user' => $user, 'token' => $token
            ]
        ], 200);
    }

    public function channel($channel)
    {
        return Socialite::driver($channel)->redirect();
    }

    public function channelCallback($channel)
    {
        try {
            $user = Socialite::driver($channel)->stateless()->user();
        } catch (\Exception $error) {
            return redirect(env('CLIENT_BASE_URL') . '/auth/sign-in?error=Unable to login using ' . $channel . '. Please try again');
        }

        $userExist = User::where('email', $user->email)->first();
        $token = null;

        if (!$userExist) {
            $newUser = new User();
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->save();

            $newUser->assignRole('user');

            $user_profile = new UserProfile();
            $user_profile->fk_user_id = $newUser->id;
            $user_profile->avatar = $user->avatar;
            $user_profile->save();

            $token = $newUser->createToken('my-app-token')->plainTextToken;
        } else
            $token = $userExist->createToken('my-app-token')->plainTextToken;

        return redirect(
            env('CLIENT_BASE_URL') . '/auth/social-callback?token=' . $token . '&name=' . $user->name . '&email=' . $user->email
        );
    }


}
