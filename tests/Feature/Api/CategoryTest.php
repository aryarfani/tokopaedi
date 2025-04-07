<?php

use App\Models\Category;

test('get all categories', function () {
    Category::factory()->count(5)->create();

    $response = $this->getJson('/api/categories');

    $response->assertStatus(200)
        ->assertJsonCount(5);
});

