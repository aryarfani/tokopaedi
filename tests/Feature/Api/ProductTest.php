<?php

use App\Models\Product;
use App\Models\Category;

test('get all products', function () {
    $response = $this->getJson('/api/products');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
                'category_id',
                'price',
                // Add other relevant fields here
            ],
        ],
    ]);
});

test('filter products by name', function () {
    Product::factory()->create(['name' => 'Product 1']);
    Product::factory()->create(['name' => 'Product 2']);

    $response = $this->getJson('/api/products?name=Product 1');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('filter products by category id', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['category_id' => $category->id]);
    Product::factory()->create(['category_id' => $category->id + 1]);

    $response = $this->getJson("/api/products?category_id={$category->id}");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('filter products by min price', function () {
    Product::factory()->create(['price' => 10000]);
    Product::factory()->create(['price' => 20000]);

    $response = $this->getJson('/api/products?min_price=15000');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('filter products by max price', function () {
    Product::factory()->create(['price' => 10000]);
    Product::factory()->create(['price' => 20000]);

    $response = $this->getJson('/api/products?max_price=15000');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('filter products by min and max price', function () {
    Product::factory()->create(['price' => 10000]);
    Product::factory()->create(['price' => 20000]);

    $response = $this->getJson('/api/products?min_price=15000&max_price=25000');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

