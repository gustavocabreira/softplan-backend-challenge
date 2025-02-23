<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\Subscriber;
use Illuminate\Console\Command;

class SendEmailToSubscriberCommand extends Command
{
    protected $signature = 'subscriber:send-email';

    protected $description = 'Sends an email to a subscriber';

    public function handle(): void
    {
        Subscriber::query()
            ->with('cake')
            ->whereHas('cake', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->where('notified_at', null)
            ->where('status', 'pending')
            ->get()
            ->each(function (Subscriber $subscriber) {
                SendEmailJob::dispatch($subscriber->cake->id, $subscriber->cake->name, $subscriber->email)->onQueue('email');
                $subscriber->update(['status' => 'processing']);
            });
    }
}
