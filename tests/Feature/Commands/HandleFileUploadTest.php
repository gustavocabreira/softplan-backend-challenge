<?php

use App\Helpers\GenerateCsvData;
use App\Jobs\HandleFileUploadJob;
use App\Models\Cake;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;

test('it should be able to dispatch a job when file is uploaded', function () {
    Queue::fake();

    $cake = Cake::factory()->create();

    $file = UploadedFile::fake()->createWithContent('emails.csv', GenerateCsvData::execute(1000));
    $path = $file->storeAs('uploads', uniqid().'.csv');

    $cake->emailLists()->create(['file_path' => $path, 'status' => 'pending']);

    $this->artisan('handle:upload');

    Queue::assertPushed(HandleFileUploadJob::class);
});
