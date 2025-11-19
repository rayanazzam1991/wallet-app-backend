<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('logs in successfully with correct credentials', function () {
    $password = 'secret123';

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => $password,
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure([
        'data' => [
            'id',
            'name',
            'email',
            'token',
            'token_type',
        ]
    ]);

    expect($response['data']['token_type'])->toBe('Bearer');
});

it('fails to login with wrong password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'correct-password',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422);

    $response->assertJsonValidationErrors([
        'credentials' => 'The provided credentials are incorrect.'
    ]);
});

it('fails to login when user does not exist', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'missing@example.com',
        'password' => 'secret123',
    ]);

    $response->assertStatus(422); // because firstOrFail()
});

it('returns authenticated user using me() endpoint', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->getJson('/api/auth/me');

    $response->assertStatus(200);

    $response->assertJson([
        'data' => [
            'id' => $user->id,
            'email' => $user->email,
        ]
    ]);
});
