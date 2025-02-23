<?php

use App\Enums\UploadedListStatus;
use App\Helpers\GenerateCsvData;
use App\Jobs\ProcessEmailJob;
use App\Models\Cake;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

test('it should dispatch the ProcessEmailJob', function () {
    Bus::fake();

    $csvData = GenerateCsvData::execute(1000);
    $file = UploadedFile::fake()->createWithContent('emails.csv', $csvData);
    $filePath = $file->store('email_lists');

    $cake = Cake::factory()->create(['quantity' => 10]);
    $cake->uploadedLists()->create([
        'file_path' => $filePath,
        'status' => UploadedListStatus::Pending,
    ]);

    $this->artisan('upload:handle');

    Bus::assertBatched(function ($batch) {
        return $batch->jobs->contains(fn ($job) => $job instanceof ProcessEmailJob) && $batch->jobs->count() === 2;
    });

    Storage::delete($filePath);
});
