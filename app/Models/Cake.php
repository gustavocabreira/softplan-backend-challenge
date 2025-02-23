<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Cake extends Model
{
    use HasFactory;
    use Searchable;

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

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'quantity' => (int) $this->quantity,
        ];
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscriber::class);
    }

    public function uploadedLists(): HasMany
    {
        return $this->hasMany(UploadedList::class);
    }
}
