<?php

class UserResponse
{
    public static function register($user, $token)
    {
        return response()->json([
            'status' => 'account created',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public static function login($user, $token)
    {
        return response()->json([
            'status' => 'logged in',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public static function logout($user)
    {
        return response()->json([
            'status' => 'logged out',
            'user' => $user
        ], 200);
    }

    public static function refresh($user, $token)
    {
        return response()->json([
            'status' => 'token refreshed',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public static function me($user)
    {
        return response()->json([
            'status' => 'profile fetched',
            'user' => $user
        ], 200);
    }

    public static function update($user)
    {
        return response()->json([
            'status' => 'profile updated',
            'user' => $user
        ], 200);
    }


}
