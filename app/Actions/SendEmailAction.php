<?php

namespace App\Actions;

use App\Models\Subscriber;
use App\Notifications\CakeAvailableNotification;

class SendEmailAction
{
    public function execute(int $cakeId, string $cakeName, string $email): void
    {
        $subscriber = Subscriber::query()
            ->where('cake_id', $cakeId)
            ->where('email', $email)
            ->first();

        if ($subscriber->cake->quantity == 0) {
            $subscriber->update(['notified_at' => now(), 'status' => 'done']);

            return;
        }

        $subscriber->notify(new CakeAvailableNotification($cakeName, $email));
        $subscriber->update(['notified_at' => now(), 'status' => 'done']);
    }
}
