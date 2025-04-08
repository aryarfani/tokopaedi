<?php

use App\Models\Category;

test('get all categories', function () {
    Category::factory()->count(5)->create();

    $response = actingAsAdmin()->get('/categories');

    $response->assertStatus(200)
        ->assertSee(Category::first()->name);
});

test('store category', function () {
    $response = actingAsAdmin()->post('/categories', [
        'name' => 'New Category',
    ]);

    $response->assertStatus(302)
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('success', 'Category created successfully');

    $this->assertDatabaseHas('categories', ['name' => 'New Category']);
});

test('update category', function () {
    $category = Category::factory()->create(['name' => 'Old Name']);

    $response = actingAsAdmin()->put("/categories/{$category->id}", [
        'name' => 'Updated Name',
    ]);

    $response->assertStatus(302)
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('success', 'Category updated successfully');
    $this->assertDatabaseHas('categories', ['name' => 'Updated Name']);
});

test('delete category', function () {
    $category = Category::factory()->create();

    $response = actingAsAdmin()->delete("/categories/{$category->id}");

    $response->assertStatus(302)
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('success', 'Category deleted successfully');
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('edit category', function () {
    $category = Category::factory()->create();

    $response = actingAsAdmin()->get("/categories/{$category->id}/edit");

    $response->assertStatus(200)
        ->assertSee($category->name);
});

test('create category', function () {
    $response = actingAsAdmin()->get('/categories/create');

    $response->assertStatus(200)
        ->assertSee('Add New Category');
});
