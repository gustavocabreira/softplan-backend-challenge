<?php

use App\Jobs\SendEmailJob;
use App\Models\Cake;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Queue;

test('it should be able to dispatch a job when file is uploaded', function () {
    Queue::fake();

    $cake = Cake::factory()->create(['quantity' => 1000]);
    Subscriber::factory()->count(10)->create(['cake_id' => $cake->id, 'email' => fake()->email]);

    $this->artisan('email:send');

    Queue::assertPushed(SendEmailJob::class, 10);
});

test('it should not dispatch a job when there are no subscribers', function () {
    Queue::fake();

    Cake::factory()->create(['quantity' => 100]);

    $this->artisan('email:send');

    Queue::assertNotPushed(SendEmailJob::class);
});

test('it should not dispatch a job when there are no cakes', function () {
    Queue::fake();

    $this->artisan('email:send');

    Queue::assertNotPushed(SendEmailJob::class);
});

test('it should not dispatch a job when there is not a cake with quantity', function () {
    Queue::fake();

    Cake::factory()->create(['quantity' => 0]);
    Subscriber::factory()->create(['cake_id' => 1, 'email' => fake()->email]);

    $this->artisan('email:send');

    Queue::assertNotPushed(SendEmailJob::class);
});
