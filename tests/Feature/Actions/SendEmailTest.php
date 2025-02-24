<?php

use App\Actions\SendEmailAction;
use App\Models\Cake;
use App\Models\Subscriber;
use App\Notifications\CakeAvailableNotification;
use Illuminate\Support\Facades\Notification;

test('it should send an email', function () {
    Notification::fake();

    $cake = Cake::factory()->create(['quantity' => 10]);
    $subscriber = Subscriber::factory()->create(['cake_id' => $cake->id, 'email' => fake()->email]);

    (new SendEmailAction)->execute($cake->id, $cake->name, $subscriber->email);

    $this->assertDatabaseHas('subscribers', [
        'id' => $subscriber->id,
        'cake_id' => $cake->id,
        'email' => $subscriber->email,
        'status' => 'done',
    ]);

    Notification::assertSentTo($subscriber, CakeAvailableNotification::class, fn (CakeAvailableNotification $notification) => expect($notification)
        ->toBeInstanceOf(CakeAvailableNotification::class)
        ->and($notification->email)->toBe($subscriber->email)
        ->and($notification->cake)->toBe($cake->name)
        ->and($notification->toMail($subscriber)->subject)->toBe($cake->name.' - Cake Available')
        ->and($notification->toMail($subscriber)->introLines)->toContain('The cake is available!')
    );
});

test('it should not send an email when the cake is out of stock', function () {
    Notification::fake();

    $cake = Cake::factory()->create(['quantity' => 0]);
    $subscriber = Subscriber::factory()->create(['cake_id' => $cake->id, 'email' => fake()->email]);

    (new SendEmailAction)->execute($cake->id, $cake->name, $subscriber->email);

    $this->assertDatabaseHas('subscribers', [
        'id' => $subscriber->id,
        'cake_id' => $cake->id,
        'email' => $subscriber->email,
        'status' => 'done',
    ]);

    Notification::assertNotSentTo($subscriber, CakeAvailableNotification::class);
});
