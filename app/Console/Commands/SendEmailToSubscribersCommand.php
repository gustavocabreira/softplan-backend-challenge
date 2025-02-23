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
        $subscribers = Subscriber::query()
            ->whereHas('cake', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->where('status', 'pending')
            ->limit(5000)
            ->get();

        $subscribers->each(function ($subscriber) {
            SendEmailJob::dispatch($subscriber->cake->id, $subscriber->cake->name, $subscriber->email)->onQueue('email');
        });
    }
}
