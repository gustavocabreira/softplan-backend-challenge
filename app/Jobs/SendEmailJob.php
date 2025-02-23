<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Notifications\CakeAvailableNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Throwable;

class SendEmailJob implements ShouldQueue
{
    use Batchable, Queueable;

    public int $tries = 3;

    public bool $unique = true;

    public array $backoff = [30, 60, 90];

    public function __construct(
        private readonly int $cakeId,
        private readonly string $cakeName,
        private readonly string $email,
    ) {}

    public function tags(): array
    {
        return ['email', 'cake_id:'.$this->cakeId, 'email:'.$this->email];
    }

    public function handle(): void
    {
        $subscriber = Subscriber::query()
            ->where('cake_id', $this->cakeId)
            ->where('email', $this->email)
            ->first();

        $subscriber->notify(new CakeAvailableNotification($this->cakeName, $this->email));
        $subscriber->update(['notified_at' => now(), 'status' => 'done']);
    }

    public function middleware(): array
    {
        $key = 'send_email:'.md5($this->cakeId.':'.$this->email);

        return [
            new WithoutOverlapping($key),
        ];
    }

    public function failed(?Throwable $throwable): void
    {
        if ($this->shouldRetry($throwable)) {
            SendemailJob::dispatch($this->cakeId, $this->cakeName, $this->email)->onQueue('email');
            $this->delete();

            return;
        }

        if ($this->shouldInspect($throwable)) {
            SendemailJob::dispatch($this->cakeId, $this->cakeName, $this->email)->onQueue('inspect-email');
            $this->delete();
            return;
        }

        Subscriber::query()->where('cake_id', $this->cakeId)->where('email', $this->email)->delete();
    }

    private function shouldRetry(Throwable $throwable): bool
    {
        return str_contains($throwable->getMessage(), 'has been closed unexpectedly');
    }

    private function shouldInspect(Throwable $throwable): bool
    {
        return str_contains($throwable->getMessage(), 'got empty code');
    }
}
