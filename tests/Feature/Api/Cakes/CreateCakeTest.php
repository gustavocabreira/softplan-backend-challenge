<?php

use App\Models\Cake;
use Illuminate\Http\Response;

test('it should be able to create a cake', function () {
    $model = new Cake;
    $payload = Cake::factory()->make()->toArray();

    $response = $this->postJson(route('api.cakes.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), [
        'id' => 1,
        ...$payload,
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);
});
