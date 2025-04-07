<?php

use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Http\UploadedFile;

test('get all recipes', function () {
    Recipe::factory()->count(5)->create();

    $response = using($this)->getJson('/api/recipes');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data.data');
});

test('get recipes by title', function () {
    $recipe = Recipe::factory()->create();

    $response = using($this)->getJson("/api/recipes?title={$recipe->title}");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data.data');
});

test('get recipes by category', function () {
    $recipe = Recipe::factory()->create();

    $response = using($this)->getJson("/api/recipes?category_id={$recipe->category_id}");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data.data');
});

test('get recipes by category and title', function () {
    $recipe = Recipe::factory()->create();

    $response = using($this)->getJson("/api/recipes?category_id={$recipe->category_id}&title={$recipe->title}");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data.data');
});

test('get recipes by category and title returns 0 result when title is unknown', function () {
    $recipe = Recipe::factory()->create();

    $response = using($this)->getJson("/api/recipes?category_id={$recipe->category_id}&title=unknown");

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data.data');
});

test('get recipes by category and title returns 0 result when category is unknown', function () {
    $recipe = Recipe::factory()->create();

    $response = using($this)->getJson("/api/recipes?category_id=unknown&title={$recipe->title}");

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data.data');
});

test('get recipes by category and title returns 0 result when both are unknown', function () {
    $recipe = Recipe::factory()->create();

    $response = using($this)->getJson("/api/recipes?category_id=unknown&title=unknown");

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data.data');
});

test('get single recipe', function () {
    $recipe = Recipe::factory()->create();

    $response = using($this)->getJson("/api/recipes/{$recipe->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $recipe->id);
});

test('get single recipe not found', function () {
    $response = using($this)->getJson("/api/recipes/999");

    $response->assertStatus(404);
});

test('get recipes without id not found', function () {
    $response = using($this)->getJson("/api/recipes/null");

    $response->assertStatus(404);
});

test('store recipe', function () {
    $category = Category::factory()->create();

    $response = using($this)->postJson('/api/recipes', [
        'title' => 'Recipe 1',
        'description' => 'This is a test recipe',
        'image' => UploadedFile::fake()->image('recipe.jpg'),
        'category_id' => $category->id
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Recipe created successfully',
            'data' => [
                'title' => 'Recipe 1',
                'description' => 'This is a test recipe',
                'category_id' => $category->id
            ]
        ]);
});

test('store recipe with empty title', function () {
    $category = Category::factory()->create();

    $response = using($this)->postJson('/api/recipes', [
        'title' => '',
        'description' => 'This is a test recipe',
        'image' => UploadedFile::fake()->image('recipe.jpg'),
        'category_id' => $category->id
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('title');
});

test('store recipe with empty description', function () {
    $category = Category::factory()->create();

    $response = using($this)->postJson('/api/recipes', [
        'title' => 'Recipe 1',
        'description' => '',
        'image' => UploadedFile::fake()->image('recipe.jpg'),
        'category_id' => $category->id
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['description']);
});

test('store recipe without image', function () {
    $category = Category::factory()->create();

    $response = using($this)->postJson('/api/recipes', [
        'title' => 'Recipe 1',
        'description' => 'This is a test recipe',
        'category_id' => $category->id
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('image');
});

test('update recipe', function () {
    $recipe = Recipe::factory()->create();

    $response = using($this)->postJson("/api/recipes/$recipe->id/update", [
        'title' => 'Recipe 2',
        'description' => 'This is an updated recipe',
        'image' => UploadedFile::fake()->image('recipe.jpg'),
        'category_id' => $recipe->category_id
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'category_id',
                'created_at',
                'updated_at'
            ]
        ]);

    $this->assertDatabaseHas('recipes', [
        'id' => $recipe->id,
        'title' => 'Recipe 2',
        'description' => 'This is an updated recipe',
        'category_id' => $recipe->category_id
    ]);
});

test('delete recipe', function () {
    $recipe = Recipe::factory()->create();

    $response = using($this)->deleteJson("/api/recipes/{$recipe->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Recipe deleted successfully'
        ]);

    $this->assertDatabaseMissing('recipes', [
        'id' => $recipe->id,
    ]);
});

