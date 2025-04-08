<?php

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('get single product', function () {
    $product = Product::factory()->create();

    $response = actingAsAdmin()->get(route('products.index', $product));

    $response->assertStatus(200)
        ->assertSee($product->name);
});

test('guest cannot access product create page', function () {
    $this->get(route('products.create'))->assertRedirect(route('login'));
});

test('admin can access product create page', function () {
    actingAsAdmin()->get(route('products.create'))->assertOk();
});

test('admin can store product', function () {
    $product = Product::factory()->make()->toArray();
    $product['image'] = UploadedFile::fake()->image('product.png');

    actingAsAdmin()->post(route('products.store'), $product)
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', [
        'name' => $product['name'],
        'price' => $product['price'],
    ]);
});

test('admin can edit product page', function () {
    $product = Product::factory()->create();

    actingAsAdmin()->get(route('products.edit', $product))->assertOk();
});

test('admin can update product', function () {
    $product = Product::factory()->create();

    actingAsAdmin()->put(route('products.update', $product), [
        'name' => 'Updated Product',
        'price' => 1000,
        'description' => 'Updated Description',
        'category_id' => $product->category_id,
    ]);

    $this->assertDatabaseHas('products', [
        'name' => 'Updated Product',
        'price' => 1000,
    ]);
});

test('admin can update product with image', function () {
    $product = Product::factory()->create();

    actingAsAdmin()->post(route('products.update', $product), [
        'name' => 'Updated Product',
        'price' => 1000,
        'description' => 'Updated Description',
        'category_id' => $product->category_id,
        'image' => UploadedFile::fake()->image('product.png'),
        '_method' => 'PUT',
    ]);

    $this->assertDatabaseHas('products', [
        'name' => 'Updated Product',
        'price' => 1000,
    ]);

    $product->refresh();

    // Storage::assertExists($product->image);
});

test('admin can delete product', function () {
    $product = Product::factory()->create();

    actingAsAdmin()->delete(route('products.destroy', $product));

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});
