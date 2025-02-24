<?php

namespace App\Console\Commands;

use App\Jobs\HandleFileUploadJob;
use App\Models\EmailList;
use Illuminate\Console\Command;

class HandleFileUploadCommand extends Command
{
    protected $signature = 'handle:upload';

    protected $description = 'This command is used to handle the file upload';

    public function handle(): void
    {
        EmailList::query()
            ->with('cake')
            ->where('status', 'pending')
            ->get()
            ->each(function (EmailList $list) {
                $list->update(['status' => 'processing']);
                $job = new HandleFileUploadJob(
                    cakeId: $list->cake->id,
                    cakeName: $list->cake->name,
                    listId: $list->id,
                    filePath: $list->file_path
                );
                dispatch($job);
            });
    }
}
