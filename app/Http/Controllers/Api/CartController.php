<?php

namespace App\Http\Controllers\Api;

use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function getCartItems()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();

        return response()->json([
            'data' => $cartItems
        ]);
    }

    public function addToCart(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:1'],
        ]);

        CartItem::updateOrCreate(
            [
                'product_id' => $validatedData['product_id'],
                'user_id' => Auth::id()
            ],
            ['quantity' => $validatedData['quantity']]
        );

        return response()->json([
            'message' => 'Successfully added to cart.'
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $validatedData = $request->validate([
            'cart_id' => 'required',
        ]);

        CartItem::where('id', $validatedData['cart_id'])->delete();

        return response()->json([
            'message' => 'Successfully removed from cart.'
        ]);
    }
}
