<?php

use App\Jobs\MarkSubscribersAsPending;
use App\Models\Cake;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

test('it should be able to update the cake', function () {
    $cake = Cake::factory()->create();

    $payload = Cake::factory()->make()->toArray();

    $response = $this->putJson(route('api.cakes.update', [
        'cake' => $cake->id,
    ]), $payload);

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure($cake->getFillable())
        ->assertJson([
            'id' => $cake->id,
            ...$payload,
        ]);

    $this->assertDatabaseHas($cake->getTable(), [
        'id' => $cake->id,
        ...$payload,
    ]);
});

test('it should return not found when the cake does not exist', function () {
    $payload = Cake::factory()->make()->toArray();

    $response = $this->putJson(route('api.cakes.update', [
        'cake' => -1,
    ]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonStructure([
            'message',
        ]);
});

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => 'The name field is required.'],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => 'The name field must not be greater than 255 characters.'],
    ],
    'empty weight' => [
        ['weight' => ''], ['weight' => 'The weight field is required.'],
    ],
    'empty price' => [
        ['price' => ''], ['price' => 'The price field is required.'],
    ],
    'empty quantity' => [
        ['quantity' => ''], ['quantity' => 'The quantity field is required.'],
    ],
    'invalid weight' => [
        ['weight' => -1], ['weight' => 'The weight must be greater than or equal to 1.'],
    ],
    'invalid price' => [
        ['price' => -1], ['price' => 'The price must be greater than or equal to 0.'],
    ],
    'invalid quantity' => [
        ['quantity' => -1], ['quantity' => 'The quantity must be greater than or equal to 0.'],
    ],
    'invalid weight type' => [
        ['weight' => 'a'], ['weight' => 'The weight must be an integer.'],
    ],
    'invalid price type' => [
        ['price' => 'a'], ['price' => 'The price must be a numeric.'],
    ],
    'invalid quantity type' => [
        ['quantity' => 'a'], ['quantity' => 'The quantity must be an integer.'],
    ],
]);

test('it should return unprocessable entity when payload is invalid', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $cake = Cake::factory()->create();

    $response = $this->putJson(route('api.cakes.update', [
        'cake' => $cake->id,
    ]), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $this->assertDatabaseMissing($cake->getTable(), [
        'id' => $cake->id,
        ...$payload,
    ]);

    $this->assertDatabaseCount($cake->getTable(), 1);
})->with('invalid_payload');

test('it should dispatch a job when cake is available', function () {
    Queue::fake();
    $cake = Cake::factory()->create(['quantity' => 0]);

    $payload = Cake::factory()->make(['quantity' => 1])->toArray();

    $this->putJson(route('api.cakes.update', [
        'cake' => $cake->id,
    ]), $payload);

    Queue::assertPushed(MarkSubscribersAsPending::class);
});
