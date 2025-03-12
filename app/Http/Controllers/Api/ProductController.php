<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        request()->validate([
            'name' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'min_price' => ['nullable', 'numeric'],
            'max_price' => ['nullable', 'numeric'],
        ]);

        $products = Product::query()->with('category')
            ->when(request()->query('name'), function ($query, $name) {
                $query->where('name', 'LIKE', "%{$name}%");
            })
            ->when(request()->query('category_id'), function ($query, $category_id) {
                $query->where('category_id', $category_id);
            })
            ->when(request()->query('min_price'), function ($query, $min_price) {
                $query->where('price', '>=', $min_price);
            })
            ->when(request()->query('max_price'), function ($query, $max_price) {
                $query->where('price', '<=', $max_price);
            })
            ->paginate(10);

        return response()->json($products);
    }
}
