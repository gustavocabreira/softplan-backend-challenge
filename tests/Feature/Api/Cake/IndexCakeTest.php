<?php

use App\Models\Cake;
use Illuminate\Http\Response;

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
