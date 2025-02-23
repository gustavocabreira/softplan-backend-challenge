<?php

namespace App\Actions;

use App\Jobs\ProcessEmailJob;
use App\Jobs\SendBatchedEmailJob;
use App\Models\Cake;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;

class HandleListUploadAction
{
    public function execute(UploadedFile $file, Cake $cake): void
    {
        $emails = $this->getEmails($file);

        $chunks = $emails->chunk(10000)->map(fn ($chunk) => new ProcessEmailJob($cake->id, $chunk->toArray()));

        Bus::batch($chunks)
            ->name('import-emails')
            ->then(fn () => $this->dispatchEmails($cake, $emails))
            ->dispatch();
    }

    private function getEmails(UploadedFile $file): Collection
    {
        $lines = file($file->getRealPath());
        $emails = collect($lines);
        $emails->shift();

        return $emails->map(fn ($email) => trim($email));
    }

    private function dispatchEmails(Cake $cake, Collection $emails): void
    {
        $emails->chunk(10000)->map(fn ($chunk) => SendBatchedEmailJob::dispatch($cake->id, $cake->name, $chunk->toArray())->onQueue('email'));
    }
}
