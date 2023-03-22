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
        $orders = Order::with('product', 'AgroInput', 'customer')->where('customer_id', Auth::id())->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Orders found',
            'data' => $orders
        ], 200);
    }

    public function viewOrders(Request $request)
    {
        $user = Auth::user();
        $farms = Farm::where('farmer_id', $user->id)->get();
        $shops = SupplierShop::where('supplier_id', $user->id)->get();
        $orders = Order::with('customer', 'product', 'AgroInput')->whereIn('supplier_shop_id', $shops->pluck('id'))->orWhereIn('farm_id', $farms->pluck('id'))->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Orders found',
            'data' => $orders
        ], 200);
    }
    public function show($id)
    {
        $order = Order::with('product', 'customer', 'agroInput')->where('id', $id)->first();
        if (!$order) {
            return response()->json([
                'status' => 'not found',
                'message' => 'Order not found'
            ], 200);
        }

        if (
            ($order->product && $order->product->farmer_id != auth()->user()->id) &&
            ($order->AgroInput && $order->AgroInput->supplier_id != auth()->user()->id) &&
            $order->customer->id != auth()->user()->id
        ) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You are not authorized to view this order'
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order found',
            'data' => $order
        ], 200);
    }

    public function store(Request $request)
    {

        if (!$request->product_id && !$request->agro_input_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Either product or agro input is required'
            ], 200);
        }

        if ($request->product_id && $request->agro_input_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only one of product or agro input is required'
            ], 200);
        }

        try {
            if ($request->agro_input_id) {
                $agroInput = AgroInput::find($request->agro_input_id);
            }
            if ($request->product_id) {
                $product = Product::find($request->product_id);
            }

            $order = Order::where('product_id', $request->product_id)->where('customer_id', auth()->user()->id)->first();
            if ($order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already ordered this product',
                ], 200);
            }
            $order = Order::create([
                'product_id' => $request->product_id,
                'AgroInput_id' => $request->agro_input_id,
                'customer_id' => auth()->user()->id,
                'farm_id' => $request->farm_id,
                'supplier_shop_id' => $request->supplier_shop_id,
                'deliver_method' => $request->deliver_method,
                'payment_method' => $request->payment_method,
                'delivery_address' => $request->delivery_address,
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
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $order = Order::with('farm', 'supplierShop', 'product')->where('id', $id)->first();
        if (($order->farm && $order->farm->farmer_id != Auth::id()) && ($order->supplierShop && $order->supplierShop->supplier_id != Auth::id())) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You are not authorized to update this order'
            ], 200);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'data' => $validator->errors()
            ], 200);
        }

        $order->update([
            'status' => $request->status
        ]);
        $transaction = TransactionController::createTransaction($order->id, $order->product->price ?? $order->agroInput->price, 'cash', $order->status ?? 'pending', Now());

        return response()->json([
            'status' => 'success',
            'message' => 'Order updated',
            'data' => $order,
            'transaction' => $transaction->original
        ], 200);
    }


    public function destroy($id)
    {
        $order = Order::with('customer', 'transaction')->where('id', $id)->first();
        if ($order->transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order cannot be deleted',
                'data' => 'Order has been approved and cannot be deleted'
            ], 200);
        }
        try {
            if (
                $order->customer->id != Auth::id() &&
                $order->farmer_id != Auth::id() &&
                $order->supplier_id != Auth::id()
            ) {
                return response()->json([
                    'status' => 'unauthorized',
                    'message' => 'You are not authorized to delete this order'
                ], 200);
            }

            $order->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Order deleted',
                'data' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => 'Order not found'
            ], 200);
        }
    }

    public function view_farm_orders($farmId)
    {
        $farm = Farm::findOrFail($farmId);
        if (!$farm) {
            return response()->json([
                'status' => 'not found',
                'message' => 'No farm found with id ' . $farmId . ''
            ], 200);
        }
        if ($farm->farmer_id != Auth::id()) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You are not authorized to view this farm orders'
            ], 200);
        }
        $orders = $farm->orders()->with('product')->get();
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'not found',
                'message' => 'No orders found'
            ], 200);
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
            ], 200);
        }
        if ($supplyShop->supplier_id != Auth::id()) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You are not authorized to view this supplier orders'
            ], 200);
        }
        $orders = $supplyShop->orders()->with('agroInput')->get();
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'not found',
                'message' => 'No orders found'
            ], 200);
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
