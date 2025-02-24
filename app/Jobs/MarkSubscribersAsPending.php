<?php

namespace App\Jobs;

use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MarkSubscribersAsPending implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly int $cakeId) {}

    public function handle(): void
    {
        Subscriber::query()
            ->where('cake_id', $this->cakeId)
            ->where('status', 'done')
            ->update(['status' => 'pending', 'notified_at' => null]);
    }
}
