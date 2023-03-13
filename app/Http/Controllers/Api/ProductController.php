<?php

namespace App\Http\Controllers\Api;

use App\Models\Farm;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


/**
 * Summary of ProductController
 */
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('farm')->get();
        return response()->json([
            'message' => 'success',
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::with('farm')->find($id);
        return response()->json([
            'message' => 'success',
            'data' => $product
        ]);
    }

    public function farmProduct($farmId){
        $farm = Farm::find($farmId);
        if(!$farm){
            return response()->json([
                'status' => 'errpr',
                'message' => 'farm not found'
            ]);
        }

        $products = Product::with('farmer')->where('id',$farmId)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Products successfully retrieved',
            'date' => $products
        ]);
    }

    public function search($key)
    {
        $products = Product::with('farm')
        ->where('name', 'like', '%' . $key . '%')
        ->orWhere('description', 'like', '%' . $key . '%')
        ->orWhere('price', 'like', '%' . $key . '%')
        ->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Products successfully retrieved',
            'data' => $products
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'farm_id' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'data' => $validator->errors()
            ]);
        }
        try {
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'farm_id' => $request->farm_id,
            ]);

            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $product->image = $name;
            $product->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'data' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'error',
                'data' => 'Product not found'
            ]);
        }
        try {

            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->farm_id = $request->farm_id;

            if ($request->image) {
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                $image->move($destinationPath, $name);
                $product->image = $name;
            }
            $product->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'data' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $product = Product::with('farmer')->where('id', $id)->first();
        if (!$product) {
            return response()->json([
                'message' => 'error',
                'data' => 'Product not found'
            ]);
        }
        if ($product->farmer->id != auth()->user()->id) {
            return response()->json([
                'message' => 'error',
                'data' => 'You are not authorized to delete this product'
            ]);
        }
        try {
            $image_path = public_path('/images/' . $product->image);
            unlink($image_path);
            $product->delete();
            return response()->json([
                'message' => 'success',
                'data' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'data' => $e->getMessage()
            ]);
        }
    }
}
