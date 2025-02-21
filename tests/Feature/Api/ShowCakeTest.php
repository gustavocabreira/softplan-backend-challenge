<?php

use App\Models\Cake;
use Illuminate\Http\Response;

test('it should be able to show the selected cake', function () {
    $cake = Cake::factory()->create();

    $response = $this->getJson(route('api.cakes.show', [
        'cake' => $cake->id,
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure($cake->getFillable())
        ->assertJson([
            'id' => $cake->id,
            'name' => $cake->name,
            'weight' => $cake->weight,
            'price' => $cake->price,
            'quantity' => $cake->quantity,
        ]);
});

test('it should return not found when the cake does not exist', function () {
    $response = $this->getJson(route('api.cakes.show', [
        'cake' => -1,
    ]));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonStructure([
            'message',
        ]);
});
