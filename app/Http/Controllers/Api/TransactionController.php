<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use App\Http\Controllers\Controller;


class TransactionController extends Controller
{
    public static function createTransaction($order_id, $price, $payment_method, $status, $transaction_date)
    {
        try{
            $transaction = Transaction::create([
                'order_id' => $order_id,
                'total_price' => $price,
                'transaction_date' => $transaction_date,
                'status' => $status,
                'payment_method' => $payment_method
            ]);
            return response()->json([
                'status' => true,
                'message' => 'transaction created',
                'data' => $transaction
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public static function updateTransaction($order)
    {
        $transaction = Transaction::where('order_id', $order->id)->first();
        $transaction->amount = $order->amount;
        $transaction->save();
    }

    public static function deleteTransaction($order)
    {
        $transaction = Transaction::where('order_id', $order->id)->first();
        $transaction->delete();
    }


}
