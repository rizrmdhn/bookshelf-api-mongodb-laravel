<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Helpers\ValidatorHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ],
                [
                    'email.required' => 'Please fill your email',
                    'password.required' => 'Please fill your password'
                ]
            );

            if (!$token = auth()->guard('api')->attempt($validator->validated())) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }



            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
                'user' => auth()->guard('api')->user()
            ], 'Authenticated');
        } catch (\Throwable $error) {
            return ResponseFormatter::error(null, $error->getMessage(), 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $user = new User();

            ValidatorHelper::validateRequest(
                $request,
                [
                    'name' => 'required|string|max:255',
                    'username' => 'required|string|max:255|unique:users',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8|confirmed',
                    'password_confirmation' => 'required|string|min:8',
                ],
                [
                    'name.required' => 'Please fill your name',
                    'username.required' => 'Please fill your username',
                    'username.unique' => 'Username already exists',
                    'email.required' => 'Please fill your email',
                    'email.unique' => 'Email already exists',
                    'password.required' => 'Please fill your password',
                    'password.confirmed' => 'Password confirmation does not match'
                ]
            );

            $user->name = $request->name;
            $user->username = $request->username;
            $user->email  = $request->email;
            $user->password = Hash::make($request->password);

            $user->save();

            return ResponseFormatter::success([
                'user' => $user
            ], 'User Registered');
        } catch (\Throwable $error) {
            return ResponseFormatter::error(null, $error->getMessage(), 500);
        }
    }

    public function logout()
    {
        auth()->guard('api')->logout();
        return ResponseFormatter::success([
            'message' => 'Logged Out'
        ], 'Logout Success');
    }

    public function userProfile(Request $request)
    {

        $request->user()->makeHidden(['password']);
        return ResponseFormatter::success($request->user(), 'Data user berhasil diambil');
    }
}
