<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate();

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'max:255'],
            'price' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'image' => ['required', 'array'],
            'image.*' => ['required', 'image'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $product = Product::create($validatedData);

        foreach ($validatedData['images'] as $value) {
            $product->images()->create([
                'image' => $value->store('images', 'public')
            ]);
        }

        return to_route('products.index')
            ->with('success', 'Product created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
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

        return back()->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product deleted successfully');
    }
}
