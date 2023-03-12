<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupplierShop;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplyShopController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $supplyShops = SupplierShop::with('user')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Supply shops fetched successfully',
            'supplyShops' => $supplyShops
        ]);

    }

    public function user_supply_shops($userId): \Illuminate\Http\JsonResponse
    {
        $user = User::find($userId);
        if($user){
            return response()->json([
                'status' => 'error',
                'message' => 'No user found with such Id'
            ]);
        }
        $supplyShops = SupplierShop::where('user_id', $userId)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Supply shops fetched successfully',
            'supplyShops' => $supplyShops
        ]);
    }

    public function show($id){
        $supplyShop = SupplierShop::with('user')->find($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Supply shop fetched successfully',
            'supplyShop' => $supplyShop
        ]);
    }

    public function search($key): \Illuminate\Http\JsonResponse
    {
        $supplyShops = SupplierShop::where('name', 'like', '%'.$key.'%')
        ->orWhere('location', 'like', '%'.$key.'%')
        ->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Supply shops fetched successfully',
            'supplyShops' => $supplyShops
        ]);
    }


    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required|string',
            'location' => 'required|string',
        ]);

        if ($validators->fails()) {
            return response()->json($validators->errors()->toJson(), 400);
        }

        $user = auth()->user();
        try {
            $supplyShop = SupplierShop::create([
                'name' => $request->name,
                'location' => $request->location,
                'user_id' => $user->id
            ]);

            if(!$user->hasRole('supplier')){
                $user->assignRole('supplier');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Supply shop created successfully',
                'supplyShop' => $supplyShop
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating supply shop',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required|string',
            'location' => 'required|string',
            'id' => 'required|integer'
        ]);

        if ($validators->fails()) {
            return response()->json($validators->errors()->toJson(), 400);
        }

        $supplyShop = SupplierShop::find($id);
        if(!$supplyShop){
            return response()->json([
                'status' => 'error',
                'message' => 'No supply shop found with such Id'
            ]);
        }

        $user = auth()->user();
        if($user->id !== $supplyShop->user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to update this supply shop'
            ]);
        }

        try {
            $supplyShop->update([
                'name' => $request->name,
                'location' => $request->location,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Supply shop updated successfully',
                'supplyShop' => $supplyShop
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating supply shop',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $supplyShop = SupplierShop::find($id);
        if(!$supplyShop){
            return response()->json([
                'status' => 'error',
                'message' => 'No supply shop found with such Id'
            ]);
        }

        $user = auth()->user();
        if($user->id !== $supplyShop->user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to delete this supply shop'
            ]);
        }

        try {
            $supplyShop->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Supply shop deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting supply shop',
                'error' => $e->getMessage()
            ]);
        }
    }
}
