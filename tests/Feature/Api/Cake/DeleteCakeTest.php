<?php

use App\Models\Cake;
use Illuminate\Http\Response;

test('it should be able to delete a cake', function () {
    $cake = Cake::factory()->create();

    $response = $this->deleteJson(route('api.cakes.destroy', [
        'cake' => $cake->id,
    ]));

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing($cake->getTable(), [
        'id' => $cake->id,
    ]);
});

test('it should return not found when the cake does not exist', function () {
    $response = $this->deleteJson(route('api.cakes.destroy', [
        'cake' => -1,
    ]));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonStructure([
            'message',
        ]);
});
