<?php

namespace App\Jobs;

use App\Actions\HandleFileUploadAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleFileUploadJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $cakeId,
        public string $cakeName,
        public int $listId,
        public string $filePath
    ) {}

    public function handle(): void
    {
        (new HandleFileUploadAction)->execute(
            cakeId: $this->cakeId,
            cakeName: $this->cakeName,
            listId: $this->listId,
            filePath: $this->filePath
        );
    }
}
