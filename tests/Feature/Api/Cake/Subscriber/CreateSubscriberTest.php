<?php

use App\Models\Cake;
use App\Models\Subscriber;
use Illuminate\Http\Response;

test('it should be able to create a subscriber', function () {
    $cake = Cake::factory()->create();

    $payload = [
        'email' => 'user@example.com',
    ];

    $response = $this->postJson(route('api.cakes.subscribers.store', [
        'cake' => $cake->id,
    ]), $payload);

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure([
            'id',
            'cake_id',
            'email',
            'status',
            'created_at',
            'updated_at',
            'cake',
        ]);

    $this->assertDatabaseHas('subscribers', [
        'id' => 1,
        'cake_id' => $cake->id,
        'email' => 'user@example.com',
        'status' => 'pending',
    ]);
});

test('it should return not found if the cake does not exist', function () {
    $payload = [
        'email' => 'user@example.com',
    ];

    $response = $this->postJson(route('api.cakes.subscribers.store', [
        'cake' => 1,
    ]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonStructure([
            'message',
        ]);

    $this->assertDatabaseMissing('subscribers', [
        'cake_id' => 1,
        'email' => 'user@example.com',
    ]);
});

dataset('invalid_payload', [
    'empty email' => [
        ['email' => null], ['email' => 'The email field is required.'],
    ],
    'not a valid email' => [
        ['email' => 'invalid'], ['email' => 'The email field must be a valid email address.'],
    ],
]);

test('it should return validation errors if the payload is invalid', function (array $payload, array $expectedErrors) {
    $key = array_keys($expectedErrors);
    $cake = Cake::factory()->create();

    $response = $this->postJson(route('api.cakes.subscribers.store', [
        'cake' => $cake->id,
    ]), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $this->assertDatabaseMissing('subscribers', [
        'cake_id' => $cake->id,
        'email' => $payload['email'],
    ]);
})->with('invalid_payload');

test('it should return you are already subscribed if you are already subscribed', function () {
    $cake = Cake::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'cake_id' => $cake->id,
        'email' => 'user@example.com',
    ]);

    $response = $this->postJson(route('api.cakes.subscribers.store', [
        'cake' => $cake->id,
    ]), [
        'email' => 'user@example.com',
    ]);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'message',
        ]);

    expect($response->json('message'))->toBe('This email is already subscribed to this cake.');

    $this->assertDatabaseHas('subscribers', [
        'id' => $subscriber->id,
        'cake_id' => $cake->id,
        'email' => 'user@example.com',
        'status' => 'pending',
    ]);
});
