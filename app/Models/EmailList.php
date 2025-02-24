<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailList extends Model
{
    use HasFactory;

    protected $table = 'email_lists';

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
