<?php

namespace App\Http\Controllers\Api;

use App\Models\Recipe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = request()->query('title');
        $category = request()->query('category_id');

        $recipes = Recipe::with('category')->latest()
            ->when($title, function ($query, $title) {
                $query->where('title', 'like', "%{$title}%");
            })
            ->when($category, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->paginate(10);

        return response()->json([
            'data' => $recipes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'image' => ['required', 'image'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $validatedData['image'] = $request->file('image')->store('images', 'public');

        $recipe = Recipe::create($validatedData);

        return response()->json([
            'message' => 'Recipe created successfully',
            'data' => $recipe,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        return response()->json([
            'data' => $recipe,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
{
        $validatedData = $request->validate([
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'image' => ['image'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        if ($request->file('image')) {
            $validatedData['image'] = $request->file('image')->store('images', 'public');
        }

        $recipe->update($validatedData);

        return response()->json([
            'message' => 'Recipe updated successfully',
            'data' => $recipe,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return response()->json([
            'message' => 'Recipe deleted successfully',
        ]);
    }
}
