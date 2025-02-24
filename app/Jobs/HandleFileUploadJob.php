<?php

namespace App\Jobs;

use App\Mail\SubscribersImportedSuccessfullyMail;
use App\Models\EmailList;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class HandleFileUploadJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $cakeId,
        private string $cakeName,
        private int $listId,
        private string $filePath
    ) {}

    public function handle(): void
    {
        $file = Storage::get($this->filePath);
        $lines = explode("\n", $file);
        array_shift($lines);
        array_pop($lines);

        $emails = collect($lines)->map(fn ($email) => trim($email));

        $chunks = $emails->chunk(10000)->map(fn ($chunk) => new ProcessEmailJob($this->cakeId, $chunk->toArray()));

        $cakeName = $this->cakeName;
        $listId = $this->listId;

        Bus::batch($chunks)
            ->name('import-emails')
            ->then(function () use ($cakeName, $listId) {
                Mail::to('user@example.com')->send(new SubscribersImportedSuccessfullyMail($cakeName));
                EmailList::query()->find($listId)->update(['status' => 'done']);
            })
            ->dispatch();
    }
}
