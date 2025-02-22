<?php

use App\Models\Cake;
use Illuminate\Http\Response;

beforeEach(function () {
    config([
        'scout.driver' => 'database',
    ]);
});

test('it should be able to index the cakes', function () {
    Cake::factory()->count(5)->create();

    $response = $this->getJson(route('api.cakes.index'));

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

test('it should be able to set how many cakes per page', function () {
    Cake::factory()->count(5)->create();

    $response = $this->getJson(route('api.cakes.index', [
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
    Cake::factory()->count(5)->create();

    $response = $this->getJson(route('api.cakes.index', [
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
    $key = array_keys($expectedErrors);

    $response = $this->getJson(route('api.cakes.index', $payload));

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);
})->with('invalid_payload');

test('it can search by name', function () {
    Cake::factory()->create(['name' => 'Banana Cake']);
    Cake::factory()->count(5)->create();

    $response = $this->getJson(route('api.cakes.index', [
        'name' => 'Banana',
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

test('it should be able to sort the cakes by name ascending', function () {
    Cake::factory()->create(['name' => 'Aaaa']);
    Cake::factory()->count(5)->create();

    $response = $this->getJson(route('api.cakes.index', [
        'order_by' => 'name',
        'direction' => 'asc',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data',
            'links',
        ]);

    expect(count($response->json('data')))->toBe(6)
        ->and($response->json('data.0.name'))->toBe('Aaaa')
        ->and($response->json('meta.current_page'))->toBe(1)
        ->and($response->json('meta.total'))->toBe(6);
});

test('it should be able to sort the cakes by name descending', function () {
    Cake::factory()->create(['name' => 'Aaaa']);
    Cake::factory()->count(5)->create();

    $response = $this->getJson(route('api.cakes.index', [
        'order_by' => 'name',
        'direction' => 'desc',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data',
            'links',
        ]);

    expect(count($response->json('data')))->toBe(6)
        ->and($response->json('data.5.name'))->toBe('Aaaa')
        ->and($response->json('meta.current_page'))->toBe(1)
        ->and($response->json('meta.total'))->toBe(6);
});
