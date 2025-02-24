<?php

namespace App\Actions;

use App\Models\Subscriber;

class MarkSubscriberAsPendingAction
{
    public function execute(int $cakeId): bool
    {
        return (bool) Subscriber::query()
            ->where('cake_id', $cakeId)
            ->where('status', 'done')
            ->update(['status' => 'pending', 'notified_at' => null]);
    }
}
