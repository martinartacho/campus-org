<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAnswer extends Model
{
    protected $fillable = [
        'event_id', 'user_id', 'question_id', 'answer'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
         return $this->belongsTo(EventQuestion::class, 'question_id');
    }
}