<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate();

        return response()->json($products);
    }
}
