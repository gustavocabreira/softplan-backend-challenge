<?php

namespace App\Jobs;

use App\Actions\MarkSubscriberAsPendingAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MarkSubscribersAsPending implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly int $cakeId) {}

    public function handle(): void
    {
        (new MarkSubscriberAsPendingAction)->execute($this->cakeId);
    }
}
