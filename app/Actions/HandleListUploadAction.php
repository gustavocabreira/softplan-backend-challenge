<?php

namespace App\Actions;

use App\Jobs\ProcessEmailJob;
use App\Jobs\SendEmailJob;
use App\Models\Cake;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;

class HandleListUploadAction
{
    public function execute(UploadedFile $file, Cake $cake): void
    {
        $emails = $this->getEmails($file);
        $chunks = $emails->chunk(500)->map(fn ($chunk) => new ProcessEmailJob($cake->id, $chunk->toArray()));

        Bus::batch($chunks)
            ->name('import-emails')
            ->then(fn () => $this->dispatchEmails($cake))
            ->dispatch();
    }

    private function getEmails(UploadedFile $file): Collection
    {
        $lines = file($file->getRealPath());
        $emails = collect($lines);
        $emails->shift();

        return $emails->map(fn ($email) => trim($email));
    }

    private function dispatchEmails(Cake $cake): void
    {
        $cake->subscribers()
            ->groupBy('email')
            ->get()
            ->each(fn ($subscriber) => SendEmailJob::dispatch($cake->id, $cake->name, $subscriber->email)->onQueue('email'));
    }
}
