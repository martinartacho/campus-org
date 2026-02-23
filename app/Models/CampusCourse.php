<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class CampusCourse extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PLANNING = 'planning';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        self::STATUS_DRAFT => 'campus.status_draft',
        self::STATUS_PLANNING => 'campus.status_planning',
        self::STATUS_IN_PROGRESS => 'campus.status_in_progress',
        self::STATUS_COMPLETED => 'campus.status_completed',
        self::STATUS_CLOSED => 'campus.status_closed',
    ];

    protected $fillable = [
        'season_id',
        'category_id',
        'code',
        'title',
        'slug',
        'description',
        'credits',
        'hours',
        'sessions',
        'max_students',
        'price',
        'level',
        'schedule',
        'start_date',
        'end_date',
        'location',
        'format',
        'is_active',
        'is_public',
        'status',
        'created_by',
        'source',
        'requirements',
        'objectives',
        'metadata',
        'space_id',
        'time_slot_id',
    ];

    protected $casts = [
        'credits' => 'integer',
        'hours' => 'integer',
        'sessions' => 'integer',
        'max_students' => 'integer',
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'schedule' => 'array',
        'requirements' => 'string',
        'objectives' => 'string',
        'metadata' => 'array'
    ];

     public const TEACHER_ROLES = [
        'main' => 'campus.teacher_role_main',
        'assistant' => 'campus.teacher_role_assistant',
        'support' => 'campus.teacher_role_support',
    ];
    
    /**
     * Get the season that owns the course.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(CampusSeason::class, 'season_id');
    }

    /**
     * Get category that owns course.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CampusCategory::class, 'category_id');
    }

    /**
     * Check if there's a course conflict in the same space and time slot.
     */
    public static function hasConflict($spaceId, $timeSlotId, $excludeCourseId = null)
    {
        $query = self::where('space_id', $spaceId)
                   ->where('time_slot_id', $timeSlotId);
        
        // Exclude current course when updating
        if ($excludeCourseId) {
            $query->where('id', '!=', $excludeCourseId);
        }
        
        return $query->exists();
    }

    /**
     * Get conflicting courses for a space and time slot.
     */
    public static function getConflicts($spaceId, $timeSlotId, $excludeCourseId = null)
    {
        $query = self::where('space_id', $spaceId)
                   ->where('time_slot_id', $timeSlotId);
        
        // Exclude current course when updating
        if ($excludeCourseId) {
            $query->where('id', '!=', $excludeCourseId);
        }
        
        return $query->get(['id', 'title', 'code', 'status']);
    }

    /**
     * Get space for the course.
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(CampusSpace::class, 'space_id');
    }

    /**
     * Get time slot for the course.
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(CampusTimeSlot::class, 'time_slot_id');
    }

    /**
     * Get the teachers for the course.
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            CampusTeacher::class,
            'campus_course_teacher',  // nom de la taula pivot
            'course_id',              // foreign key en la taula pivot per a CampusCourse
            'teacher_id',             // foreign key en la taula pivot per a CampusTeacher
            'id',                     // local key en la taula CampusCourse
            'id'                      // local key en la taula CampusTeacher
        )->withPivot('role', 'hours_assigned', 'assigned_at', 'finished_at', 'metadata')
        ->withTimestamps();
    }

    /**
     * Get the registrations for the course.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(CampusRegistration::class, 'course_id');
    } 


    /**
     * Get the students enrolled in the course.
     */
    public function students()
    {
        return $this->belongsToMany(
        CampusTeacher::class,
        'campus_course_teacher',
        'course_id',
        'teacher_id'
    );
    }

    /**
     * Scope for active courses.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for public courses.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for courses in a specific season.
     */
    public function scopeInSeason(Builder $query, $seasonId): Builder
    {
        return $query->where('season_id', $seasonId);
    }

    /**
     * Check if course has available spots.
     */
    public function hasAvailableSpots(): bool
    {
        if (is_null($this->max_students)) {
            return true;
        }
        
        $currentEnrollment = $this->registrations()
                                ->whereIn('status', ['confirmed', 'completed'])
                                ->count();
        
        return $currentEnrollment < $this->max_students;
    }

    /**
     * Get available spots count.
     */
    public function getAvailableSpotsAttribute(): int
    {
        if (is_null($this->max_students)) {
            return PHP_INT_MAX;
        }
        
        $currentEnrollment = $this->registrations()
                                ->whereIn('status', ['confirmed', 'completed'])
                                ->count();
        
        return max(0, $this->max_students - $currentEnrollment);
    }

    /**
     * Get the schedule as a formatted string.
     */
    public function getFormattedScheduleAttribute(): ?string
    {
        if (empty($this->schedule)) {
            return null;
        }
        
        return collect($this->schedule)->map(function ($day) {
            return "{$day['day']}: {$day['start']} - {$day['end']}";
        })->implode(', ');
    }

    /**
     * Check if course is currently active (within date range).
     */
    public function isCurrentlyActive(): bool
    {
        return now()->between($this->start_date, $this->end_date);
    }

       /**
     * Get main teacher for the course.
     */
    public function mainTeacher()
    {
        return $this->teachers()
            ->wherePivot('role', 'teacher')
            ->wherePivotNull('finished_at')
            ->first();
    }

    /**
     * Get active teachers for the course.
     */
    public function activeTeachers()
    {
        return $this->teachers()
            ->wherePivotNull('finished_at')
            ->get();
    }

    public function assistantTeachers()
    {
        return $this->teachers()
            ->wherePivot('role', 'assistant')
            ->get();
    }


    /**
     * Get total hours assigned to all teachers.
     */
    public function getTotalAssignedHoursAttribute(): float
    {
        return $this->teachers()
            ->wherePivotNull('finished_at')
            ->sum('hours_assigned');
    }

    /**
     * Check if a specific teacher is assigned to this course.
     */
    public function hasTeacher(int $teacherId, bool $activeOnly = true): bool
    {
        $query = $this->teachers()->where('teacher_id', $teacherId);
        
        if ($activeOnly) {
            $query->wherePivotNull('finished_at');
        }
        
        return $query->exists();
    }

    /**
     * Get teacher assignment with pivot data.
     */
    public function getTeacherAssignment(int $teacherId)
    {
        return $this->teachers()
            ->where('teacher_id', $teacherId)
            ->withPivot(['role', 'hours_assigned', 'assigned_at', 'finished_at'])
            ->first();
    }

    public function payments()
    {
        return $this->hasMany(CampusTeacherPayment::class, 'teacher_id');
    }

    public function teacherPayments()
    {
        return $this->hasMany(CampusTeacherPayment::class, 'course_id');
    }


 

}