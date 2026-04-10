<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
    ];

    /**
     * Get the task that owns the comment.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user that owns the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comment as HTML with line breaks.
     */
    public function getCommentHtmlAttribute(): string
    {
        return nl2br(e($this->comment));
    }

    /**
     * Check if comment was created recently (within last hour).
     */
    public function isRecent(): bool
    {
        return $this->created_at->gt(now()->subHour());
    }
}
