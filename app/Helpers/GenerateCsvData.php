<?php

namespace App\Helpers;

class GenerateCsvData
{
    public static function execute(int $quantity): string
    {
        $header = ['email'];
        $rows = [];

        foreach (range(0, $quantity) as $i) {
            $rows[] = ['user'.$i + 1 .'@example.com'];
        }

        return implode("\n", array_map(fn ($row) => implode(',', $row), [$header, ...$rows]));
    }
}
