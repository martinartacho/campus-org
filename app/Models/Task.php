<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'list_id',
        'title',
        'description',
        'assigned_to',
        'assigned_role',
        'priority',
        'start_date',
        'due_date',
        'status',
        'order_in_list',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the list that owns the task.
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(TaskList::class, 'list_id');
    }

    /**
     * Get the board through the list.
     */
    public function board()
    {
        return $this->hasOneThrough(TaskBoard::class, TaskList::class);
    }

    /**
     * Get the user assigned to the task.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the creator of the task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the task.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the comments for the task.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at');
    }

    /**
     * Get the attachments for the task.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class)->orderBy('created_at');
    }

    /**
     * Get the checklists for the task.
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class)->orderBy('order');
    }

    /**
     * Get the activities for the task.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TaskActivity::class)->orderBy('created_at');
    }

    /**
     * Get tasks that depend on this task.
     */
    public function dependents(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_task_id');
    }

    /**
     * Get tasks that this task depends on.
     */
    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_task_id');
    }

    /**
     * Check if task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Check if task is due soon (within 3 days).
     */
    public function isDueSoon(): bool
    {
        return $this->due_date && 
               $this->due_date->between(now(), now()->addDays(3)) && 
               $this->status !== 'completed';
    }

    /**
     * Get priority color.
     */
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'low' => '#6B7280',
            'medium' => '#F59E0B',
            'high' => '#EF4444',
            'urgent' => '#DC2626',
            default => '#6B7280',
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => '#6B7280',
            'in_progress' => '#3B82F6',
            'blocked' => '#EF4444',
            'completed' => '#10B981',
            default => '#6B7280',
        };
    }

    /**
     * Scope a query to only include tasks assigned to a user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope a query to only include tasks with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    /**
     * Log activity for this task.
     */
    public function logActivity(string $action, User $user, array $oldValues = null, array $newValues = null)
    {
        return $this->activities()->create([
            'action' => $action,
            'user_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Get completion percentage based on checklists.
     */
    public function getCompletionPercentage(): int
    {
        if ($this->checklists->isEmpty()) {
            return $this->status === 'completed' ? 100 : 0;
        }

        $completed = $this->checklists->where('is_completed', true)->count();
        $total = $this->checklists->count();

        return ($completed / $total) * 100;
    }
}
