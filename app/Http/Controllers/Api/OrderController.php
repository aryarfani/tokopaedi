<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function store()
    {
        if (!auth()->user()->cartItems()->exists()) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('setting.midtrans_server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $user = auth()->user();

        // prepare midtrans data
        $cartItems = auth()->user()->cartItems()->with('product')->get();
        foreach ($cartItems as $cartItem) {
            $item_details[] = [
                'id' => $cartItem->product->id,
                'price' => $cartItem->product->price,
                'quantity' => $cartItem->quantity,
                'name' => $cartItem->product->name,
            ];
        }

        $grossAmount = $cartItems->sum(function ($cartItem) {
            return $cartItem->product->price * $cartItem->quantity;
        });

        $orderCode = 'ORDER-' . rand(11111, 99999);

        $params = [
            'transaction_details' => [
                'order_id' => $orderCode,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            "item_details" => $item_details
        ];

        $midtransTransaction = \Midtrans\Snap::createTransaction($params);
        // $midtransTransaction = new \stdClass();

        // save order
        $order = Order::create([
            'code' => $orderCode,
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING,
            'total_price' => $grossAmount,
            'midtrans_payment_url' => @$midtransTransaction->redirect_url,
            'midtrans_snap_token' => @$midtransTransaction->token,
        ]);

        // save order items
        foreach ($cartItems as $cartItem) {
            $order->orderItems()->create([
                'product_id' => $cartItem->product->id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
            ]);
        }

        auth()->user()->cartItems()->delete();

        return response()->json([
            'data' => $order
        ]);
    }

    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('orderItems.product.category')
            ->latest()
            ->paginate();

        return response()->json([
            'data' => $orders
        ]);
    }

    public function show(Order $order)
    {
        return response()->json([
            'data' => $order->load('orderItems.product')
        ]);
    }

    public function handleCallback()
    {
        $transactionStatus = request()->transaction_status;
        $paymentType = request()->payment_type;
        $orderCode = request()->order_id;

        $order = Order::firstWhere('code', $orderCode);
        if (!isset($order)) {
            return response()->json(['message' => 'Order not found.']);
        }

        $order->midtrans_payment_type = $paymentType;

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $order->status = Order::STATUS_PAID;
        } else if (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'])) {
            $order->status = Order::STATUS_CANCELLED;
        }

        $order->save();

        return response()->json(['message' => 'Successfully processed.']);
    }
}
