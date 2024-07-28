<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];
            return ApiResponseHelper::sendResponse($data, 'Record register successful.', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
     * Login user and create token.
     */
    public function login(LoginUserRequest $request)
    {
        try {

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];
            return ApiResponseHelper::sendResponse($data, 'Login successful.', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
     * Logout user (Revoke the token).
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return ApiResponseHelper::sendResponse(null, 'Successfully logged out.', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
     * Show the profile of the authenticated user.
     */
    public function showProfile(Request $request)
    {
        try {
            return ApiResponseHelper::sendResponse($request->user(), 'Get profile successful.');
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }
}
