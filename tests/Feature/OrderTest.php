<?php

use App\Models\Order;

test('get orders', function () {
    Order::factory()
        ->hasOrderItems(3)
        ->count(5)
        ->create();

    $response = actingAsAdmin()->get('/orders');

    $response->assertStatus(200)
        ->assertSee(Order::first()->code);
});
