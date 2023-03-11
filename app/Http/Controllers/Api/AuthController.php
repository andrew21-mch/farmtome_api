<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Responses\UserResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
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

            $user->assignRole('customer');

            return UserResponse::register($user, $user->createToken('authToken')->plainTextToken);
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
        return UserResponse::login($user, $user->createToken('authToken')->plainTextToken);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'status' => 'logged out',
            'user' => $request->user()
        ], 200);
        $request->user()->token()->revoke();

        return UserResponse::logout($request->user());
    }

    public function getRoles(Request $request)
    {
        $user = $request->user();
        $user->roles = $user->getRolesNames();
        return UserResponse::me($user);
    }
}
