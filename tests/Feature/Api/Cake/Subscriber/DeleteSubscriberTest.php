<?php

use App\Models\Cake;
use App\Models\Subscriber;
use Illuminate\Http\Response;

test('it should be able to unsubscribe a subscriber', function () {
    $cake = Cake::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'cake_id' => $cake->id,
        'email' => 'user@example.com',
    ]);

    $response = $this->deleteJson(route('api.cakes.subscribers.destroy', [
        'cake' => $cake->id,
        'subscriber' => $subscriber->id,
    ]));

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing('subscribers', [
        'id' => $subscriber->id,
        'cake_id' => $cake->id,
        'email' => 'user@example.com',
    ]);
});

test('it should return not found if the cake does not exist', function () {
    $subscriber = Subscriber::factory()->create();

    $response = $this->deleteJson(route('api.cakes.subscribers.destroy', [
        'cake' => 1,
        'subscriber' => $subscriber->id,
    ]));

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('it should return not found if the subscriber does not exist', function () {
    $cake = Cake::factory()->create();

    $response = $this->deleteJson(route('api.cakes.subscribers.destroy', [
        'cake' => $cake->id,
        'subscriber' => 1,
    ]));

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});
