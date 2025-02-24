<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\Subscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SendEmailToSubscribersCommand extends Command
{
    protected $signature = 'email:send';

    protected $description = 'Command description';

    public function handle(): void
    {
        $subscribersQuery = Subscriber::query()
            ->whereHas('cake', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->where('status', 'pending')
            ->limit(5000);

        $subscribers = $subscribersQuery->get();

        $subscribersQuery->update(['status' => 'processing']);

        $jobs = $subscribers->map(function ($subscriber) {
            return new SendEmailJob($subscriber->cake->id, $subscriber->cake->name, $subscriber->email);
        });

        Bus::batch($jobs)
            ->name('send-email')
            ->onQueue('email')
            ->dispatch();
    }
}
