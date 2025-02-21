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
