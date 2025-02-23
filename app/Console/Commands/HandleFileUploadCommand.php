<?php

namespace App\Console\Commands;

use App\Jobs\HandleFileUploadJob;
use App\Models\EmailList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
                HandleFileUploadJob::dispatch($list->cake->id, $list->cake->name, $list->id, $list->file_path);
            });
    }
}
