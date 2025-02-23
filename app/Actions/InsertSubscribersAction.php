<?php

namespace App\Actions;

use App\Models\Subscriber;

class InsertSubscribersAction
{
    public static function execute(int $cakeId, array $emails): void
    {
        $subscribers = Subscriber::query()
            ->where('cake_id', $cakeId)
            ->whereIn('email', $emails)
            ->get();

        $notSubscribed = collect($emails)
            ->diff($subscribers->pluck('email'))
            ->map(fn ($email) => ['email' => trim($email), 'cake_id' => $cakeId]);

        if ($notSubscribed->isNotEmpty()) {
            Subscriber::query()->insert($notSubscribed->toArray());
        }
    }
}
