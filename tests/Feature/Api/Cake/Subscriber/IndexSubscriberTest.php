<?php

use App\Models\Cake;
use App\Models\Subscriber;
use Illuminate\Http\Response;

test('it should be able to list the subscribers', function () {
    $cake = Cake::factory()->create();
    Subscriber::factory()->count(5)->create(['cake_id' => $cake->id]);

    $response = $this->getJson(route('api.cakes.subscribers.index', [
        'cake' => $cake->id,
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data',
            'links',
        ]);

    expect(count($response->json('data')))->toBe(5)
        ->and($response->json('meta.current_page'))->toBe(1)
        ->and($response->json('meta.total'))->toBe(5);
});

test('it should be able to set how many subscribers per page', function () {
    $cake = Cake::factory()->create();
    Subscriber::factory()->count(5)->create(['cake_id' => $cake->id]);

    $response = $this->getJson(route('api.cakes.subscribers.index', [
        'cake' => $cake->id,
        'per_page' => 2,
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data',
            'links',
        ]);

    expect(count($response->json('data')))->toBe(2)
        ->and($response->json('meta.per_page'))->toBe(2)
        ->and($response->json('meta.current_page'))->toBe(1)
        ->and($response->json('meta.total'))->toBe(5);
});

test('it should be able to change the current page', function () {
    $cake = Cake::factory()->create();
    Subscriber::factory()->count(5)->create(['cake_id' => $cake->id]);

    $response = $this->getJson(route('api.cakes.subscribers.index', [
        'cake' => $cake->id,
        'per_page' => 2,
        'page' => 2,
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data',
            'links',
        ]);

    expect(count($response->json('data')))->toBe(2)
        ->and($response->json('meta.per_page'))->toBe(2)
        ->and($response->json('meta.current_page'))->toBe(2)
        ->and($response->json('meta.total'))->toBe(5);
});

dataset('invalid_payload', [
    'per_page as string' => [
        ['per_page' => 'string'], ['per_page' => 'The per_page must be an integer.'],
    ],
    'per_page as float' => [
        ['per_page' => 1.1], ['per_page' => 'The per_page must be an integer.'],
    ],
    'per_page as negative' => [
        ['per_page' => -1], ['per_page' => 'The per_page must be greater than or equal to 1.'],
    ],
    'page as string' => [
        ['page' => 'string'], ['page' => 'The page must be an integer.'],
    ],
    'page as float' => [
        ['page' => 1.1], ['page' => 'The page must be an integer.'],
    ],
    'page as negative' => [
        ['page' => -1], ['page' => 'The page must be greater than or equal to 1.'],
    ],
]);

test('it should be able to return unprocessable entity when payload is invalid', function ($payload, $expectedErrors) {
    $cake = Cake::factory()->create();
    $key = array_keys($expectedErrors);

    $response = $this->getJson(route('api.cakes.subscribers.index', [
        'cake' => $cake->id,
        ...$payload,
    ]));

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);
})->with('invalid_payload');

test('it can search by email', function () {
    $cake = Cake::factory()->create();
    Subscriber::factory()->count(5)->create(['cake_id' => $cake->id]);
    Subscriber::factory()->create(['cake_id' => $cake->id, 'email' => 'user@example.com']);

    $response = $this->getJson(route('api.cakes.subscribers.index', [
        'cake' => $cake->id,
        'email' => 'user@example.com',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data',
            'links',
        ]);

    expect(count($response->json('data')))->toBe(1)
        ->and($response->json('meta.current_page'))->toBe(1)
        ->and($response->json('meta.total'))->toBe(1);
});

test('it should return not found when trying to list subscribers of a cake that does not exist', function () {
    $response = $this->getJson(route('api.cakes.subscribers.index', [
        'cake' => -1,
    ]));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonStructure([
            'message',
        ]);
});

test('it should be able to sort the subscribers by email ascending', function () {
    $cake = Cake::factory()->create();
    Subscriber::factory()->count(5)->create(['cake_id' => $cake->id]);
    Subscriber::factory()->create(['cake_id' => $cake->id, 'email' => 'aaa@example.com']);

    $response = $this->getJson(route('api.cakes.subscribers.index', [
        'cake' => $cake->id,
        'order_by' => 'email',
        'direction' => 'asc',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data',
            'links',
        ]);

    expect(count($response->json('data')))->toBe(6)
        ->and($response->json('data.0.email'))->toBe('aaa@example.com')
        ->and($response->json('meta.current_page'))->toBe(1)
        ->and($response->json('meta.total'))->toBe(6);
});

test('it should be able to sort the subscribers by email descending', function () {
    $cake = Cake::factory()->create();
    Subscriber::factory()->count(5)->create(['cake_id' => $cake->id]);
    Subscriber::factory()->create(['cake_id' => $cake->id, 'email' => 'zzz@example.com']);

    $response = $this->getJson(route('api.cakes.subscribers.index', [
        'cake' => $cake->id,
        'order_by' => 'email',
        'direction' => 'desc',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data',
            'links',
        ]);

    expect(count($response->json('data')))->toBe(6)
        ->and($response->json('data.0.email'))->toBe('zzz@example.com')
        ->and($response->json('meta.current_page'))->toBe(1)
        ->and($response->json('meta.total'))->toBe(6);
});
