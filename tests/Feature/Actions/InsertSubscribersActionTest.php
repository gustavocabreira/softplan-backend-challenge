<?php

use App\Actions\InsertSubscribersAction;
use App\Models\Cake;
use App\Models\Subscriber;

test('it should insert the subscribers', function () {
    $subscriber = new Subscriber;
    $cakeId = Cake::factory()->create()->id;

    $emails = ['email1@example.com', 'email2@example.com', 'email3@example.com'];

    InsertSubscribersAction::execute($cakeId, $emails);

    $this->assertDatabaseHas($subscriber->getTable(), [
        'cake_id' => $cakeId,
        'email' => 'email1@example.com',
    ]);

    $this->assertDatabaseHas($subscriber->getTable(), [
        'cake_id' => $cakeId,
        'email' => 'email2@example.com',
    ]);

    $this->assertDatabaseHas($subscriber->getTable(), [
        'cake_id' => $cakeId,
        'email' => 'email3@example.com',
    ]);

    $this->assertDatabaseCount($subscriber->getTable(), 3);
});
