<?php

use App\Helpers\GenerateCsvData;
use App\Models\Cake;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

test('it should be able to create a cake without file', function () {
    $model = new Cake;
    $payload = Cake::factory()->make()->toArray();

    $response = $this->postJson(route('api.cakes.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), [
        'id' => 1,
        ...$payload,
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);
    $this->assertDatabaseCount('email_lists', 0);
});

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => 'The name field is required.'],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => 'The name field must not be greater than 255 characters.'],
    ],
    'empty weight' => [
        ['weight' => ''], ['weight' => 'The weight field is required.'],
    ],
    'empty price' => [
        ['price' => ''], ['price' => 'The price field is required.'],
    ],
    'empty quantity' => [
        ['quantity' => ''], ['quantity' => 'The quantity field is required.'],
    ],
    'invalid weight' => [
        ['weight' => -1], ['weight' => 'The weight must be greater than or equal to 1.'],
    ],
    'invalid price' => [
        ['price' => -1], ['price' => 'The price must be greater than or equal to 0.'],
    ],
    'invalid quantity' => [
        ['quantity' => -1], ['quantity' => 'The quantity must be greater than or equal to 0.'],
    ],
    'invalid weight type' => [
        ['weight' => 'a'], ['weight' => 'The weight must be an integer.'],
    ],
    'invalid price type' => [
        ['price' => 'a'], ['price' => 'The price must be a numeric.'],
    ],
    'invalid quantity type' => [
        ['quantity' => 'a'], ['quantity' => 'The quantity must be an integer.'],
    ],
]);

test('it should return unprocessable entity when payload is invalid', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $model = new Cake;

    $response = $this->postJson(route('api.cakes.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 0);
})->with('invalid_payload');

test('it should be able to create a cake with file', function () {
    $model = new Cake;
    $payload = Cake::factory()->make()->toArray();

    $csvData = GenerateCsvData::execute(50000);
    $file = UploadedFile::fake()->createWithContent('emails.csv', $csvData);
    $payload['file'] = $file;

    $response = $this->postJson(route('api.cakes.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure([
            ...$model->getFillable(),
            'email_lists' => [
                '*' => [
                    'id',
                    'file_path',
                    'status',
                ],
            ],
        ]);

    unset($payload['file']);
    $this->assertDatabaseHas($model->getTable(), [
        'id' => 1,
        ...$payload,
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);
    $this->assertDatabaseCount('email_lists', 1);
});
