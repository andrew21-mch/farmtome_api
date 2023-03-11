<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use UserResponse;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'phone' => 'required|string|unique:users',
        ]);

        if ($validators->fails()) {
            return response()->json($validators->errors()->toJson(), 400);
        }

        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'location' => $request->location,
            ]);

            UserResponse::register($user, $user->createToken('authToken')->accessToken);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 500);
        }

    }

    public function login(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validators->fails()) {
            return response()->json($validators->errors()->toJson(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        UserResponse::login($user, $user->createToken('authToken')->accessToken);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        UserResponse::logout($request->user());
    }
}
