<?php

namespace App\Actions;

use App\Jobs\ProcessEmailJob;
use App\Mail\SubscribersImportedSuccessfullyMail;
use App\Models\EmailList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class HandleFileUploadAction
{
    public function execute(int $cakeId, string $cakeName, int $listId, string $filePath): void
    {
        $emails = $this->getEmails($filePath);

        $chunks = $emails->chunk(10000)->map(fn ($chunk) => new ProcessEmailJob($cakeId, $chunk->toArray()));

        Bus::batch($chunks)
            ->name('import-emails')
            ->then(function () use ($cakeName, $listId) {
                Mail::to('user@example.com')->send(new SubscribersImportedSuccessfullyMail($cakeName));

                EmailList::query()
                    ->find($listId)
                    ->update(['status' => 'done']);
            })
            ->dispatch();
    }

    private function getEmails(string $filePath): Collection
    {
        $file = Storage::get($filePath);
        $lines = explode("\n", $file);
        array_shift($lines);
        array_pop($lines);

        return collect($lines)->map(fn ($email) => trim($email));
    }
}
