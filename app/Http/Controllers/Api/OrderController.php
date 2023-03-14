<?php

namespace App\Http\Controllers\Api;

use App\Models\AgroInput;
use App\Models\Farm;
use App\Models\Order;
use App\Models\Product;
use App\Models\SupplierShop;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class OrderController extends Controller
{
    public function viewUserOrders(Request $request)
    {
        $orders = Order::with('product', 'AgroInput')->where('customer_id', Auth::id())->get();
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'not found',
                'message' => 'No orders found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Orders found',
            'data' => $orders
        ], 200);
    }

    public function show($id)
    {
        $order = Order::with('product', 'createdBy', 'agroInput')->where('id', $id)->first();
        if (!$order) {
            return response()->json([
                'status' => 'not found',
                'message' => 'Order not found'
            ], 404);
        }

        if (
            ($order->product && $order->product->farmer_id != auth()->user()->id) &&
            ($order->AgroInput && $order->AgroInput->supplier_id != auth()->user()->id) &&
            $order->createdBy->id != auth()->user()->id
        ) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You are not authorized to view this order'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order found',
            'data' => $order
        ], 200);
    }

    public function store(Request $request)
    {

        if (!$request->product_id && !$request->AgroInput_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'data' => 'Either product_id or AgroInput_id is required'
            ], 422);
        }

        if ($request->product_id && $request->AgroInput_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'data' => 'Only one of product_id or AgroInput_id is required'
            ], 422);
        }

        try {
            if ($request->AgroInput_id) {
                $agroInput = AgroInput::find($request->AgroInput_id);
            }
            if ($request->product_id) {
                $product = Product::find($request->product_id);
            }

            $order = Order::create([
                'product_id' => $request->product_id,
                'AgroInput_id' => $request->AgroInput_id,
                'customer_id' => auth()->user()->id,
                'farm_id' => $request->farm_id,
                'supplier_shop_id' => $request->supplier_shop_id,
                'status' => 'pending',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order created',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => 'Product not found'
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $order = Order::with('farm', 'supplierShop', 'product')->where('id', $id)->first();
        if (($order->farm && $order->farm->farmer_id != Auth::id()) && ($order->supplierShop && $order->supplierShop->supplier_id != Auth::id())) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You are not authorized to update this order'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'data' => $validator->errors()
            ], 422);
        }

        $order->update([
            'status' => $request->status
        ]);
        if ($order->status == 'approved') {
            Transaction::create([
                'order_id' => $order->id,
                'total_price' => $order->product->price ?? $order->agroInput->price,
                'transaction_date' => Now(),
                'status' => 'pending',
                'payment_method' => 'cash',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order updated',
            'data' => $order
        ], 200);
    }


    public function destroy($id)
    {
        $order = Order::with('createdBy', 'transaction')->where('id', $id)->first();
        if($order->transaction){
            return response()->json([
                'status' => 'error',
                'message' => 'Order cannot be deleted',
                'data' => 'Order has been approved and cannot be deleted'
            ], 422);
        }
        if (
            $order->customer_id != Auth::id() &&
            $order->farmer_id != Auth::id() &&
            $order->supplier_id != Auth::id()
        ) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You are not authorized to delete this order'
            ], 401);
        }

        $order->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Order deleted',
            'data' => $order
        ], 200);
    }

    public function view_farm_orders($farmId)
    {
        $farm = Farm::findOrFail($farmId);
        if (!$farm) {
            return response()->json([
                'status' => 'not found',
                'message' => 'No farm found with id ' . $farmId . ''
            ], 401);
        }
        if ($farm->farmer_id != Auth::id()) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You are not authorized to view this farm orders'
            ], 401);
        }
        $orders = $farm->orders()->with('product')->get();
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'not found',
                'message' => 'No orders found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Orders found',
            'data' => $orders
        ], 200);
    }

    public function view_supply_shop_orders($supplyShopId)
    {
        $supplyShop = SupplierShop::find($supplyShopId);
        if (!$supplyShop) {
            return response()->json([
                'status' => 'not found',
                'message' => 'No supply shop found with id ' . $supplyShopId . ''
            ], 401);
        }
        if ($supplyShop->supplier_id != Auth::id()) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You are not authorized to view this supplier orders'
            ], 401);
        }
        $orders = $supplyShop->orders()->with('agroInput')->get();
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'not found',
                'message' => 'No orders found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Orders found',
            'data' => $orders
        ], 200);
    }



// 1 - view user orders ( viewUserOrders($userId) )
// 2 - view farm orders ( view_farm_orders($farmId) )
// 3 - view supply shop orders ( view_supply_shop_orders($supplyShopId) )
// 4 - create order ( create(Request $request) )
// 5 - update order ( update(Request $request, $id) )
// 6 - delete order ( delete($id) )
// 7 - view order ( show($id) )


}
