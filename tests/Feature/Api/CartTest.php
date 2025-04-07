<?php

use App\Models\Product;
use App\Models\CartItem;
use App\Models\User;

test('it gets cart items', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    using($this)->actingAs($user)
        ->postJson('/api/carts', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

    $response = using($this)->actingAs($user)
        ->getJson('/api/carts');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'product_id',
                    'user_id',
                    'quantity',
                    'product' => [
                        'id',
                        'name',
                        'price',
                        'category' => [
                            'id',
                            'name'
                        ]
                    ]
                ]
            ]
        ]);
});

test('it adds item to cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = using($this)->actingAs($user)
        ->postJson('/api/carts', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'Successfully added to cart.'
        ]);

    expect(CartItem::where('user_id', $user->id)
        ->where('product_id', $product->id)
        ->where('quantity', 1)
        ->exists())->toBeTrue();
});

test('it fails to add item to cart with invalid product id', function () {
    $user = User::factory()->create();

    $response = using($this)->actingAs($user)
        ->postJson('/api/carts', [
            'product_id' => 999,
            'quantity' => 1
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('product_id');
});

test('it fails to add item to cart with invalid quantity', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = using($this)->actingAs($user)
        ->postJson('/api/carts', [
            'product_id' => $product->id,
            'quantity' => 0
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('quantity');
});

test('it removes item from cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    using($this)->actingAs($user)
        ->postJson('/api/carts', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

    $cartItem = CartItem::where('user_id', $user->id)
        ->where('product_id', $product->id)
        ->first();

    $response = using($this)->actingAs($user)
        ->postJson('/api/carts/remove', [
            'cart_id' => $cartItem->id
        ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'Successfully removed from cart.'
        ]);

    expect(CartItem::where('id', $cartItem->id)->exists())->toBeFalse();
});

