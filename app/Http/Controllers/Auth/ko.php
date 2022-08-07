<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Jobs\VerifyRegistrationJob;
use App\Mail\VerifyRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /*
    |  user || admin || kitchen manager registration
    */
    public function register(RegistrationRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'work_id' => $validatedData['work_id'],
        ]);

        VerifyRegistrationJob::dispatch($user);

        // $token = $user->createToken($user->name)->plainTextToken;

        $response = [
            'user' => $user,
        ];
        return response($response, 200);
    }

    /*
     |  user || admin || kitchen manager login
     */
    public function login(LoginRequest $request)
    {

        $request->ensureIsNotRateLimited(); // check user login attempts

        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (!$user->email_verified_at) {
                throw ValidationException::withMessages(['message' => [trans('auth.notVerified')]]);
            }

            if ($user->is_active != User::IS_ACTIVE) {
                throw ValidationException::withMessages(['message' => [trans('auth.active')]]);
            }

            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken($user->name)->plainTextToken;
                $userRoles = $user->roles(); // user role
                $roles = $userRoles->pluck('name'); // user name role
                $permissions = $userRoles->with('permissions')->get()->pluck('permissions')->flatten()->pluck('name')->toArray(); // user role permission
                $user->roles = $roles;
                $user->permissions = $permissions;
                $response = [
                    'api_token' => $token,
                    'user' => $user,
                    'roles' => $roles,
                    'permissions' => $permissions,
                ];
                RateLimiter::clear($request->throttleKey()); // clear user login attempts
                return response($response, 200);
            } else {
                RateLimiter::hit($request->throttleKey()); // add user login attempts
                throw ValidationException::withMessages(['message' => [trans('auth.password')]]);
            }

        } else {
            RateLimiter::hit($request->throttleKey()); // add user login attempts
            throw ValidationException::withMessages(['message' => [trans('auth.failed')]]);
        }
    }

    /**
     * Verifies user token.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function verify_token(Request $request)
    {
        $request->validate([
            'api_token' => 'required'
        ]);

        $token = PersonalAccessToken::findToken($request->api_token);
        $user = $token->tokenable;
        /*
         | Check token is expired or not
         | If expired then return error
         */
        $token_expires_time = env('SANCTUM_TOKEN_EXPIRATION');
        $current_time_to_token_create_time_diff = verify_token_expiration($token->created_at);
        if ($current_time_to_token_create_time_diff > $token_expires_time) {
            throw ValidationException::withMessages(['api_token' => ['Token expired']]);
        }
        /*
        | Check user is valid or not
        */
        if (!$user) {
            throw ValidationException::withMessages(['api_token' => ['Invalid token']]);
        }

        $userRoles = $user->roles(); // user role
        $roles = $userRoles->pluck('name'); // user name role
        $permissions = $userRoles->with('permissions')->get()->pluck('permissions')->flatten()->pluck('name')->toArray(); // user role permission
        $user->roles = $roles;
        $user->permissions = $permissions;
        $response = [
            'api_token' => $request->api_token,
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
        ];
        return response($response, 200);
    }

    //verify email
    public function verify_email($email)
    {
        $user = User::where('email', $email)->first();
        if ($user  && $user->email_verified_at == null) {
            $user->email_verified_at = Carbon::now();
            $user->save();
            return redirect(env('CLIENT_BASE_URL').'/sign-in');
        }else
            return redirect(env('CLIENT_BASE_URL').'/404?msg='.trans('auth.alreadyVerified'));
    }

    /*
     | Remove user token
    */
    public function logout(Request $request)
    {
        $token = $request->user()->tokens();
        $token->delete();
        $response = 'You have been successfully logged out!';
        return response($response, 200);
    }
}
