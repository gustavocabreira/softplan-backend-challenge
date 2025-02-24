<?php

use App\Helpers\GenerateCsvData;
use App\Jobs\HandleFileUploadJob;
use App\Models\Cake;
use Illuminate\Http\UploadedFile;

test('it should be able to insert emails into subscribers table', function () {
    config(['queue.default' => 'sync']);

    $cake = Cake::factory()->create();
    $file = UploadedFile::fake()->createWithContent('emails.csv', GenerateCsvData::execute(1000));
    $path = $file->storeAs('uploads', uniqid().'.csv');
    $emailList = $cake->emailLists()->create(['status' => 'pending', 'file_path' => $path])->id;

    HandleFileUploadJob::dispatchSync($cake->id, $cake->name, $emailList, $path);

    $this->assertDatabaseHas('email_lists', [
        'id' => $emailList,
        'status' => 'done',
    ]);

    $this->assertDatabaseCount('subscribers', 1000);
});
