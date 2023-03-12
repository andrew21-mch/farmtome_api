<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FarmController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $farms = Farm::with('user')->get();
        return response()->json([
            'message' => 'Farms fetched successfully',
            'farms' => $farms
        ]);

    }

    public function user_farms($userId): \Illuminate\Http\JsonResponse
    {
        $farms = Farm::where('user_id', $userId)->get();
        return response()->json([
            'message' => 'Farms fetched successfully',
            'farms' => $farms
        ]);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $farm = Farm::find($id);
        return response()->json([
            'message' => 'Farm fetched successfully',
            'farm' => $farm
        ]);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required|string',
            'location' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        if ($validators->fails()) {
            return response()->json($validators->errors()->toJson(), 400);
        }

        try {
            $farm = Farm::create([
                'name' => $request->name,
                'location' => $request->location,
                'user_id' => $request->user_id,
            ]);

            return response()->json([
                'message' => 'Farm created successfully',
                'farm' => $farm
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $farm = Farm::find($id);
        if (!$farm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farm not found'
            ], 404);
        }

        if ($user->id != $farm->user_id) {
            return response()->json([
                'status' => 'Unauthorized',
                'messages' => 'You are not the owner of this farm so you can not edit it'
            ]);
        }

        try {
            if ($request->name) {
                $farm->name = $request->name;
            }

            if ($request->location) {
                $farm->location = $request->location;
            }

            if ($request->location) {
                $farm->location = $request->location;
            }

            $farm->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Farm successfully updated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $farm = Farm::find($id);
        if (!$farm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Farm not found'
            ]);
        }

        $farm->delete();
        return response()->json([
            'status' => 'success',
            'message' => "Farm successfully deleted"
        ]);
    }
}
