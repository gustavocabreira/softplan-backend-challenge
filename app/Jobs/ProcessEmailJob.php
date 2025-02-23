<?php

namespace App\Jobs;

use App\Models\Subscriber;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessEmailJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(private readonly int $cakeId, private readonly array $emails) {}

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $subscribers = Subscriber::query()->whereIn('email', $this->emails)->get();

        $notSubscribed = collect($this->emails)
            ->diff($subscribers->pluck('email'))
            ->map(fn ($email) => ['email' => trim($email), 'cake_id' => $this->cakeId]);

        if ($notSubscribed->isNotEmpty()) {
            Subscriber::query()->insert($notSubscribed->toArray());
        }
    }
}
