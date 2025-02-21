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
