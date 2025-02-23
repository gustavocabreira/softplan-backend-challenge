<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadedList extends Model
{
    protected $table = 'uploaded_lists';

    protected $fillable = [
        'cake_id',
        'file_path',
        'status',
    ];

    public function cake(): BelongsTo
    {
        return $this->belongsTo(Cake::class);
    }
}
