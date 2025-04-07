<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('successful login', function () {
    $user = User::factory()->create();

    $response = using($this)->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            'access_token',
            'token_type',
        ],
    ]);
});

test('failed login with invalid email', function () {
    $response = using($this)->postJson('/api/login', [
        'email' => 'non-existent-email@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(401);
    $response->assertJsonStructure([
        'error',
    ]);
    $this->assertEquals('Invalid Credentials', $response->json()['error']);
});

test('failed login with invalid password', function () {
    $user = User::factory()->create();

    $response = using($this)->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);
    $response->assertJsonStructure([
        'error',
    ]);
    $this->assertEquals('Invalid Credentials', $response->json()['error']);
});

test('successful registration', function () {
    $response = using($this)->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
    ]);
});

test('failed registration with invalid email', function () {
    $response = using($this)->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});

test('update user', function () {
    $user = User::factory()->create();

    $response = using($this)->actingAs($user)
        ->postJson('/api/user/update', [
            'name' => 'Jane Doe',
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'data' => [
            'name',
            'email',
        ],
    ]);
    $this->assertDatabaseHas('users', [
        'name' => 'Jane Doe',
        'email' => $user->email,
    ]);
});

test('update user with password', function () {
    $user = User::factory()->create();

    $response = using($this)->actingAs($user)
        ->postJson('/api/user/update', [
            'name' => 'Jane Doe',
            'password' => 'new-password',
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'data' => [
            'name',
            'email',
        ],
    ]);
    $this->assertDatabaseHas('users', [
        'name' => 'Jane Doe',
        'email' => $user->email,
    ]);
    $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
});

test('update user with invalid name', function () {
    $user = User::factory()->create();

    $response = using($this)->actingAs($user)
        ->postJson('/api/user/update', [
            'name' => '',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('name');
});

test('get user', function () {
    $user = User::factory()->create();

    $response = using($this)->actingAs($user)
        ->getJson('/api/user');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            'name',
            'email',
        ],
    ]);
    $this->assertEquals($user->name, $response->json()['data']['name']);
    $this->assertEquals($user->email, $response->json()['data']['email']);
});

test('logout user', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $logoutResponse = using($this)
        ->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/logout');

    $logoutResponse->assertStatus(200);
    $logoutResponse->assertJsonStructure([
        'message',
    ]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

