<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskList extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'name',
        'order',
        'color',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the board that owns the list.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(TaskBoard::class);
    }

    /**
     * Get the tasks for the list.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'list_id')->orderBy('order_in_list');
    }

    /**
     * Get pending tasks in this list.
     */
    public function pendingTasks(): HasMany
    {
        return $this->tasks()->where('status', 'pending');
    }

    /**
     * Get completed tasks in this list.
     */
    public function completedTasks(): HasMany
    {
        return $this->tasks()->where('status', 'completed');
    }

    /**
     * Get overdue tasks in this list.
     */
    public function overdueTasks(): HasMany
    {
        return $this->tasks()
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    /**
     * Get task count by priority.
     */
    public function getTaskCountByPriority($priority)
    {
        return $this->tasks()->where('priority', $priority)->count();
    }

    /**
     * Reorder tasks in this list.
     */
    public function reorderTasks(array $taskIds)
    {
        $tasks = $this->tasks()->get();
        
        foreach ($taskIds as $index => $taskId) {
            $task = $tasks->firstWhere('id', $taskId);
            if ($task) {
                $task->update(['order_in_list' => $index]);
            }
        }
    }
}
