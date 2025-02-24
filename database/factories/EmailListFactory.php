<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailList>
 */
class EmailListFactory extends Factory
{
    public function definition(): array
    {
        return [
            'file_path' => 'file_path',
            'status' => 'pending',
        ];
    }
}
