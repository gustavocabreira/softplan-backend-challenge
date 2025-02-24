<?php

use App\Helpers\GenerateCsvData;
use App\Jobs\HandleFileUploadJob;
use App\Models\Cake;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;

test('it should be able to dispatch a job when file is uploaded', function () {
    Queue::fake();
    $cake = Cake::factory()->create();
    $file = UploadedFile::fake()->createWithContent('emails.csv', GenerateCsvData::execute(10));
    $path = $file->storeAs('uploads', uniqid().'.csv');
    $emailList = $cake->emailLists()->create(['status' => 'pending', 'file_path' => $path]);

    $this->artisan('handle:upload');

    expect($emailList->refresh()->status)->toBe('processing');

    Queue::assertPushed(HandleFileUploadJob::class, 1);
});

test('it should not dispatch a job when file is already being processed', function () {
    Queue::fake();
    $cake = Cake::factory()->create();
    $file = UploadedFile::fake()->createWithContent('emails.csv', GenerateCsvData::execute(10));
    $path = $file->storeAs('uploads', uniqid().'.csv');
    $emailList = $cake->emailLists()->create(['status' => 'processing', 'file_path' => $path]);

    $this->artisan('handle:upload');

    expect($emailList->refresh()->status)->toBe('processing');

    Queue::assertNotPushed(HandleFileUploadJob::class);
});

test('it should not dispatch a job when file is already done', function () {
    Queue::fake();
    $cake = Cake::factory()->create();
    $file = UploadedFile::fake()->createWithContent('emails.csv', GenerateCsvData::execute(10));
    $path = $file->storeAs('uploads', uniqid().'.csv');
    $emailList = $cake->emailLists()->create(['status' => 'done', 'file_path' => $path]);

    $this->artisan('handle:upload');

    expect($emailList->refresh()->status)->toBe('done');

    Queue::assertNotPushed(HandleFileUploadJob::class);
});

test('it should not dispatch a job when file does not exist', function () {
    Queue::fake();

    $this->artisan('handle:upload');

    Queue::assertNotPushed(HandleFileUploadJob::class);
});
