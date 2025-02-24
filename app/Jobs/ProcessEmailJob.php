<?php

namespace App\Jobs;

use App\Actions\InsertSubscribersAction;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessEmailJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(public readonly int $cakeId, private readonly array $emails) {}

    public function handle(): void
    {
        (new InsertSubscribersAction)->execute(
            cakeId: $this->cakeId,
            emails: $this->emails
        );
    }
}
