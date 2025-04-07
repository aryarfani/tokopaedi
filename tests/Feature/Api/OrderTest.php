<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Product;

test('get all orders', function () {
    $user = User::factory()->create();

    Order::factory()
        ->for($user)
        ->hasOrderItems(3)
        ->count(5)
        ->create();

    $response = using($this)->actingAs($user)->getJson('/api/orders');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data.data');
});

test('get single order', function () {
    $order = Order::factory()
        ->hasOrderItems(3)
        ->create();

    $user = User::factory()->create();
    $response = using($this)->actingAs($user)
        ->getJson("/api/orders/{$order->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $order->id,
                'code' => $order->code,
                'status' => $order->status,
                'midtrans_payment_type' => $order->midtrans_payment_type,
                'total_price' => $order->total_price,
            ]
        ]);
});

test('store order', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    // add product to cart
    $response = using($this)->actingAs($user)
        ->postJson('/api/carts', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

    $response = $this->actingAs($user)
        ->postJson('/api/orders');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'code',
                'user_id',
                'status',
                'total_price',
                'midtrans_payment_url',
                'midtrans_snap_token',
            ]
        ]);
});

test('store order fails with empty cart', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/orders');

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Cart is empty'
        ]);
});

test('handle callback paid', function () {
    $order = Order::factory()->create();

    $callbackResponse = $this->postJson("/api/midtrans-callback", [
        'transaction_status' => 'capture',
        'payment_type' => 'bank_transfer',
        'order_id' => $order->code
    ]);

    $callbackResponse->assertStatus(200)
        ->assertJsonStructure([
            'message'
        ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'midtrans_payment_type' => 'bank_transfer',
        'status' => Order::STATUS_PAID
    ]);
});

test('handle callback cancel', function () {
    $order = Order::factory()->create();

    $callbackResponse = $this->postJson("/api/midtrans-callback", [
        'transaction_status' => 'cancel',
        'order_id' => $order->code
    ]);

    $callbackResponse->assertStatus(200)
        ->assertJsonStructure([
            'message'
        ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => Order::STATUS_CANCELLED
    ]);
});

test('handle callback with order not found', function () {
    $callbackResponse = $this->postJson("/api/midtrans-callback", [
        'transaction_status' => 'capture',
        'payment_type' => 'bank_transfer',
        'order_id' => 'NON_EXISTENT_ORDER_CODE'
    ]);

    $callbackResponse->assertStatus(200)
        ->assertJson([
            'message' => 'Order not found.'
        ]);
});

