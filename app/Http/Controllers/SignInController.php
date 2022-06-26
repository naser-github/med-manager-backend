<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Nette\InvalidStateException;

class SignInController extends Controller
{
    public function signIn(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password) || $user->status == 'inactive') {
            return response([
                'message' => ['These credentials do not match any records.']
            ], 401);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;
        unset($user->id);

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 200);
    }

//    public function facebook()
//    {
//        return Socialite::driver('facebook')->redirect();
//    }

//    public function facebookCallback()
//    {
//        $user = Socialite::driver('facebook')->user();
//    }
//
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



//    public function callback()
//    {
//        try {
//            $serviceUser = Socialite::driver('google')->user();
//        } catch (\Exception $e) {
//            return redirect(env('ADDRESS') . '/auth/social-callback?error=Unable to login using ' . . '. Please try again' . '&origin=login');
//        }
//
//        if ((env('RETRIEVE_UNVERIFIED_SOCIAL_EMAIL') == 0) && ($service != 'google')) {
//            $email = $serviceUser->getId() . '@' . $service . '.local';
//        } else {
//            $email = $serviceUser->getEmail();
//        }
//
//        $user = $this->getExistingUser($serviceUser, $email, $service);
//        $newUser = false;
//        if (!$user) {
//            $newUser = true;
//            $user = User::create([
//                'name' => $serviceUser->getName(),
//                'email' => $email,
//                'password' => ''
//            ]);
//        }
//
//        if ($this->needsToCreateSocial($user, $service)) {
//            UserSocial::create([
//                'user_id' => $user->id,
//                'social_id' => $serviceUser->getId(),
//                'service' => $service
//            ]);
//        }
//
//        return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?token=' . $this->auth->fromUser($user) . '&origin=' . ($newUser ? 'register' : 'login'));
//    }
//
//    public function needsToCreateSocial(User $user, $service)
//    {
//        return !$user->hasSocialLinked($service);
//    }
//
//    public function getExistingUser($serviceUser, $email, $service)
//    {
//        if ((env('RETRIEVE_UNVERIFIED_SOCIAL_EMAIL') == 0) && ($service != 'google')) {
//            $userSocial = UserSocial::where('social_id', $serviceUser->getId())->first();
//            return $userSocial ? $userSocial->user : null;
//        }
//        return User::where('email', $email)->orWhereHas('social', function ($q) use ($serviceUser, $service) {
//            $q->where('social_id', $serviceUser->getId())->where('service', $service);
//        })->first();
//    }


//    public function linkedIn()
//    {
//        return Socialite::driver('linkedIn')->redirect();
//    }
//
//    public function linkedInCallback()
//    {
//        $user = Socialite::driver('linkedIn')->user();
//    }
//
//    public function twitter()
//    {
//        return Socialite::driver('twitter')->redirect();
//    }
//
//    public function twitterCallback()
//    {
//        $user = Socialite::driver('twitter')->user();
//    }


}
