<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        unset($data['password_confirmation']);

        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        $userResource = new UserResource($user);

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $userResource,
            'token' => $token,
        ], 201);
    }





    public function login(LoginRequest $request)
    {
        $user = User::where(
            'email',
            $request->email
        )->first();

        if (
            !$user ||
            !Hash::check(
                $request->password,
                $user->password
            )
        ) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }



    public function me(Request $request)
{
    return new UserResource(
        $request->user()
    );
}

public function logout(Request $request)
{
    $request->user()
        ->currentAccessToken()
        ->delete();

    return response()->json([
        'message' => 'Logged out successfully.',
    ]);
}
}
