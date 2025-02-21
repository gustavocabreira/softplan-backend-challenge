<?php

use App\Models\Cake;
use Illuminate\Http\Response;

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
});
