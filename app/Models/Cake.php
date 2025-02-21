<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cake extends Model
{
    use HasFactory;

    protected $table = 'cakes';

    protected $fillable = [
        'name',
        'weight',
        'price',
        'quantity',
    ];

    protected $casts = [
        'weight' => 'integer',
        'price' => 'float',
        'quantity' => 'integer',
    ];
}
