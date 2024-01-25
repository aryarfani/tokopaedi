<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer'],
        ]);

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('setting.midtrans_server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $user = auth()->user();
        $product = Product::findOrFail($validatedData['product_id']);
        $grossAmount = $product->price * $validatedData['quantity'];

        $params = [
            'transaction_details' => [
                'order_id' => rand(11111, 99999),
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            "item_details" => [
                [
                    "id" => $product->id,
                    "price" => $product->price,
                    "quantity" => $validatedData['quantity'],
                    "name" => $product->name,
                ]
            ],
        ];

        $snapToken = \Midtrans\Snap::createTransaction($params);

        return response()->json([
            'snap_token' => $snapToken,
        ]);
    }

    public function handleCallback()
    {
        $transactionStatus = request()->transaction_status;

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            // success
        } else if (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'])) {
            // fail
        }
    }
}
