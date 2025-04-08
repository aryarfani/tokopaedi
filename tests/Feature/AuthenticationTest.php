<?php

use App\Models\User;

test('guest middleware redirects to login when accessing logout', function () {
    $response = $this->post(route('logout'));

    $response->assertStatus(302)
        ->assertRedirect(route('login'));
});

test('guest middleware redirects to login when accessing authenticated routes', function () {
    $response = $this->get(route('dashboard'));

    $response->assertStatus(302)
        ->assertRedirect(route('login'));
});

test('guest middleware allows access to register, login, and authenticate routes', function () {
    $this->get(route('register'))->assertStatus(200);
    $this->get(route('login'))->assertStatus(200);
    $this->post(route('authenticate'))->assertStatus(302);
});

test('authenticated users can access authenticated routes', function () {
    actingAsAdmin()
        ->get(route('dashboard'))
        ->assertStatus(200);
});

test('authenticated users can logout', function () {
    actingAsAdmin()
        ->post(route('logout'))
        ->assertStatus(302)
        ->assertRedirect(route('login'));
});

test('create user', function () {
    $response = actingAsAdmin()
        ->post(route('store'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'securepassword',
            'password_confirmation' => 'securepassword',
        ]);

    $response->assertStatus(302)
        ->assertSessionHas('success', 'You have successfully registered');

    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
    ]);
});

test('successful login', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = using($this)->postJson(route('authenticate'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(302);

    $this->assertAuthenticated();
});

test('failed login with missing email', function () {
    $response = using($this)->postJson(route('authenticate'), [
        'password' => 'password',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});

test('failed login with missing password', function () {
    $user = User::factory()->create();

    $response = using($this)->postJson(route('authenticate'), [
        'email' => $user->email,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('password');
});

test('failed login with wrong password', function () {
    $user = User::factory()->create();

    $response = using($this)->postJson(route('authenticate'), [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(302)
        ->assertSessionHasErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->assertSessionDoesntHaveErrors('password');
});
