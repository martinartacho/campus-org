<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CampusCourseTeacher extends Pivot
{
    use HasFactory;

    protected $table = 'campus_course_teacher';

    protected $casts = [
        'hours_assigned' => 'decimal:2',
        'assigned_at' => 'datetime',
        'finished_at' => 'date',
        'metadata' => 'array'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'course_id',
        'teacher_id',
        'role',
        'hours_assigned',
        'assigned_at',
        'finished_at',
        'metadata'
    ];

    /**
     * Get the course.
     */
    public function course()
    {
        return $this->belongsTo(CampusCourse::class);
    }

    /**
     * Get the teacher.
     */

    public function teacher()
    {
        return $this->belongsTo(CampusTeacher::class, 'teacher_id');
    }

    /**
     * Check if assignment is active.
     */
    public function isActive(): bool
    {
        return is_null($this->finished_at) || $this->finished_at > now();
    }

    /**
     * Get formatted role.
     */
    public function getFormattedRoleAttribute(): string
    {
        $roles = [
            'teacher' => 'Profesor',
            'coordinator' => 'Coordinador',
            'tutor' => 'Tutor',
            'assistant' => 'Asistente'
        ];
        
        return $roles[$this->role] ?? ucfirst($this->role);
    }
}