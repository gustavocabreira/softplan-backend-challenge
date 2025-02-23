<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Notifications\CakeAvailableNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class SendBatchedEmailJob implements ShouldQueue
{
    use Batchable, Queueable;

    public int $tries = 3;

    public bool $unique = true;

    public array $backoff = [30, 60, 90];

    public function __construct(
        private readonly int $cakeId,
        private readonly string $cakeName,
        private readonly array $emails,
    ) {}

    public function tags(): array
    {
        return ['email', 'cake_id:'.$this->cakeId];
    }

    public function handle(): void
    {
        $failedNotifications = collect();

        Subscriber::query()
            ->where('cake_id', $this->cakeId)
            ->whereIn('email', $this->emails)
            ->get()
            ->each(function (Subscriber $subscriber) use (&$failedNotifications) {
                try {
                    $subscriber->notify(new CakeAvailableNotification($this->cakeName, $this->email));
                    $subscriber->update(['notified_at' => now(), 'status' => 'done']);
                } catch (\Exception $exception) {
                    $failedNotifications->push($subscriber->email);
                }
            });

        $failedNotifications->each(fn ($email) => SendEmailJob::dispatch($this->cakeId, $this->cakeName, $email)->onQueue('email'));
    }

    public function middleware(): array
    {
        $key = 'send_email:'.md5($this->cakeId.':'.serialize($this->emails));

        return [
            new WithoutOverlapping($key),
        ];
    }
}
