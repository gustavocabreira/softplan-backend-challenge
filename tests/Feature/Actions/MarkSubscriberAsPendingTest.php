<?php

use App\Actions\MarkSubscriberAsPendingAction;
use App\Models\Cake;
use App\Models\Subscriber;

test('it should mark the subscribers as pending', function () {
    $cakeId = Cake::factory()->create()->id;
    Subscriber::factory()->count(10)->create(['cake_id' => $cakeId, 'status' => 'done']);

    (new MarkSubscriberAsPendingAction)->execute($cakeId);

    $this->assertDatabaseHas('subscribers', [
        'cake_id' => $cakeId,
        'status' => 'pending',
    ]);
});
