<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskBoard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'entity_id',
        'created_by',
        'visibility',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the lists for the board.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(TaskList::class, 'board_id')->orderBy('order');
    }

    /**
     * Get the creator of the board.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all tasks in this board through lists.
     */
    public function tasks()
    {
        return $this->hasManyThrough(Task::class, TaskList::class, 'board_id', 'list_id');
    }

    /**
     * Scope a query to only include boards of a given type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include boards visible to the user.
     */
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('visibility', 'public')
              ->orWhere('created_by', $user->id)
              ->orWhere('visibility', 'team'); // TODO: Implement team logic
        });
    }

    /**
     * Create default lists for a new board.
     */
    public function createDefaultLists()
    {
        $defaultLists = [
            ['name' => 'Pendents', 'color' => '#6B7280', 'order' => 1],
            ['name' => 'En curs', 'color' => '#3B82F6', 'order' => 2],
            ['name' => 'Bloquejat', 'color' => '#EF4444', 'order' => 3],
            ['name' => 'Fet', 'color' => '#10B981', 'order' => 4],
        ];

        foreach ($defaultLists as $listData) {
            $this->lists()->create($listData);
        }
    }

    /**
     * Get the entity that this board is linked to (course, department, etc.).
     */
    public function entity()
    {
        switch ($this->type) {
            case 'course':
                return $this->belongsTo(CampusCourse::class, 'entity_id');
            case 'department':
                // TODO: Implement department model when available
                return null;
            default:
                return null;
        }
    }
}
