<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\GeneneralController;
use App\Models\AgroInput;
use App\Models\SupplierShop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class AgroInputController extends Controller
{
    public function index()
    {
        $AgroInputs = AgroInput::with('supplierShop.supplier')->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data AgroInput',
            'data' => $AgroInputs
        ], 200);
    }

    public function show($id)
    {
        $AgroInput = AgroInput::find($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail Data AgroInput',
            'data' => $AgroInput
        ], 200);
    }

    public function search($key)
    {
        $AgroInputs = AgroInput::where('name', 'like', '%' . $key . '%')
        ->orWhere('description', 'like', '%' . $key . '%')
        ->orWhere('price', 'like', '%' . $key . '%')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data AgroInput',
            'data' => $AgroInputs
        ], 200);
    }

    public function store(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'supplier_shop_id' => 'required',
            'image' => 'required'
        ]);

        if ($validators->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validators->errors()
            ], 400);
        }

        $userShopsIds = SupplierShop::where('supplier_id', auth()->user()->id)->pluck('id');


        if(!$request->supplier_shop_id) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input',
                'data' => 'supplier_shop_id is required'
            ], 400);
        }

        if(!$userShopsIds->contains($request->supplier_shop_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input',
                'data' => 'supplier_shop_id is not valid'
            ], 400);
        }

        try {

            $image_url = GeneneralController::uploadToImgur($request->image);
            $AgroInput = AgroInput::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'supplier_id' => auth()->user()->id,
                'supplier_shop_id' => $request->supplier_shop_id,
                'image' => $image_url
            ]);

            return response()->json([
                'success' => true,
                'message' => 'AgroInput Saved',
                'data' => $AgroInput
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AgroInput Failed to Save',
                'data' => $e->getMessage()
            ], 500);
        }

    }


    public function update(Request $request, $id)
    {
        $AgroInput = AgroInput::find($id);

        if (!$AgroInput) {
            return response()->json([
                'success' => false,
                'message' => 'AgroInput not found',
                'data' => $id
            ], 404);
        }

        if($AgroInput->supplier_id != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this AgroInput',
                'data' => $id
            ], 401);
        }

        try {
            $AgroInput->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'supplier_id' => auth()->user()->id,
                'supplier_shop_id' => $request->supplier_shop_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'AgroInput Updated',
                'data' => $AgroInput
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AgroInput Failed to Update',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $AgroInput = AgroInput::find($id);

        if (!$AgroInput) {
            return response()->json([
                'success' => false,
                'message' => 'AgroInput not found',
                'data' => $id
            ], 404);
        }

        if($AgroInput->supplier_id != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this AgroInput',
                'data' => $id
            ], 401);
        }

        try {
            $AgroInput->delete();

            return response()->json([
                'success' => true,
                'message' => 'AgroInput Deleted',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AgroInput Failed to Delete',
                'data' => $e->getMessage()
            ], 500);
        }
    }

}
