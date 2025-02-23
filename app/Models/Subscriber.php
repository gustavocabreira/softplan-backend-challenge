<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Subscriber extends Model
{
    use Notifiable;

    protected $table = 'subscribers';

    protected $fillable = [
        'cake_id',
        'email',
        'notified_at',
        'status',
    ];

    public function cake(): BelongsTo
    {
        return $this->belongsTo(Cake::class);
    }
}
