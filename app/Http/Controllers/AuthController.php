<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Classes\ApiResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        DB::beginTransaction();
        try {
            $user = User::create($data);
            $token = $user->createToken('auth_token')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ];

            DB::commit();
            return ApiResponseHelper::sendResponse($response, 'User created successfully', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::sendResponse('An error occurred while creating user ' + $ex, 500);
        }
    }

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $user = User::where('email', $credentials['email'])->firstOrFail();

            if (Hash::check($credentials['password'], $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                $response = [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ];
                return ApiResponseHelper::sendResponse(['data' => $response], 'Login successful', 200);
            } else {
                return ApiResponseHelper::sendResponse('Invalid credentials', 401);
            }
        } catch (ModelNotFoundException $e) {
            return ApiResponseHelper::sendResponse('User not found', 404);
        } catch (\Exception $ex) {
            return ApiResponseHelper::sendResponse('An error occurred during login ' + $ex, 500);
        }
    }

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return ApiResponseHelper::sendResponse([], 'Logout successful', 200);
        } catch (\Exception $ex) {
            return ApiResponseHelper::sendResponse('An error occurred during logout ' + $ex, 500);
        }
    }

    public function profile()
    {
        try {
            return ApiResponseHelper::sendResponse(auth()->user(), 'Profile fetched successfully', 200);
        } catch (\Exception $ex) {
            return ApiResponseHelper::sendResponse('An error occurred while fetching profile '+$ex, 500);
        }
    }
}
