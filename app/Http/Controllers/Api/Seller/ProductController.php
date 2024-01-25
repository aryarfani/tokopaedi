<?php

namespace App\Http\Controllers\Api\Seller;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'max:255'],
            'price' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'image' => ['required', 'image'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $validatedData['image'] = $request->file('image')->store('images', 'public');
        $validatedData['seller_id'] = auth()->user()->id;
        Product::create($validatedData);

        return response()->json([
            'success' => 'Product created successfully',
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'max:255'],
            'price' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'image' => ['opsional', 'nullable', 'image'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        if (isset($request->images)) {
            $validatedData['image'] = $request->file('image')->store('images', 'public');
        }

        $product->update($validatedData);

        return response()->json([
            'success' => 'Product updated successfully',
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => 'Product deleted successfully',
        ]);
    }
}
