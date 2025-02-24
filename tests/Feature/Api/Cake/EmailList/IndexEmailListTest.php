<?php

use App\Models\Cake;
use App\Models\EmailList;
use Illuminate\Http\Response;

test('it should be able to list the email lists', function () {
    $cake = Cake::factory()->create();
    EmailList::factory()->count(5)->create(['cake_id' => $cake->id]);

    $response = $this->getJson(route('api.cakes.email-lists.index', [
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
