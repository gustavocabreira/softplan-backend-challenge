<?php

namespace App\Console\Commands;

use App\Enums\UploadedListStatus;
use App\Jobs\ProcessEmailJob;
use App\Models\UploadedList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class HandleUploadedListCommand extends Command
{
    protected $signature = 'upload:handle';

    protected $description = 'It will handle the uploaded lists';

    public function handle(): void
    {
        UploadedList::query()
            ->with('cake')
            ->where('status', UploadedListStatus::Pending)
            ->get()
            ->each(fn (UploadedList $uploadedList) => $this->handleUploadedList($uploadedList));
    }

    private function handleUploadedList(UploadedList $uploadedList): void
    {
        $uploadedList->update(['status' => UploadedListStatus::Processing]);

        $file = Storage::get($uploadedList->file_path);
        $lines = explode("\n", $file);
        $emails = collect($lines);
        $emails->shift();
        $emails->pop();

        $chunks = $emails->chunk(500)->map(fn ($chunk) => new ProcessEmailJob($uploadedList->cake_id, $chunk->toArray()));

        Bus::batch($chunks)
            ->name('import-emails')
            ->then(function () use ($uploadedList) {
                $uploadedList->update(['status' => UploadedListStatus::Done]);
            })
            ->catch(function ($exception) use ($uploadedList) {
                $uploadedList->update(['status' => UploadedListStatus::Failed]);
            })
            ->dispatch();
    }
}
