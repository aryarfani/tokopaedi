<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::paginate();

        return view('orders.index', compact('orders'));
    }
}
