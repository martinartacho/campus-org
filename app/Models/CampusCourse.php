<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CampusCourse extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $baseSlug = \Str::slug($course->title);
                
                // Si es un curso hijo, añadir sufijo para evitar duplicados
                if ($course->parent_id) {
                    $parent = static::find($course->parent_id);
                    if ($parent) {
                        $baseSlug = $baseSlug . '-edicio-' . ($parent->children()->count() + 1);
                    }
                }
                
                // Asegurar que el slug sea único
                $slug = $baseSlug;
                $count = 1;
                
                while (static::where('slug', $slug)->where('id', '!=', $course->id ?? 0)->exists()) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }
                
                $course->slug = $slug;
            }
        });

        static::updating(function ($course) {
            if ($course->isDirty('title') && empty($course->slug)) {
                $baseSlug = \Str::slug($course->title);
                
                // Si es un curso hijo, añadir sufijo para evitar duplicados
                if ($course->parent_id) {
                    $parent = static::find($course->parent_id);
                    if ($parent) {
                        $baseSlug = $baseSlug . '-edicio-' . ($parent->children()->count() + 1);
                    }
                }
                
                // Asegurar que el slug sea único
                $slug = $baseSlug;
                $count = 1;
                
                while (static::where('slug', $slug)->where('id', '!=', $course->id)->exists()) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }
                
                $course->slug = $slug;
            }
        });
    }

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

    // Level constants
    const LEVEL_BEGINNER = 'beginner';
    const LEVEL_INTERMEDIATE = 'intermediate';
    const LEVEL_ADVANCED = 'advanced';
    const LEVEL_EXPERT = 'expert';

    public const LEVELS = [
        self::LEVEL_BEGINNER => 'Principiant',
        self::LEVEL_INTERMEDIATE => 'Intermedi',
        self::LEVEL_ADVANCED => 'Avançat',
        self::LEVEL_EXPERT => 'Expert',
    ];

    protected $fillable = [
        'season_id',
        'category_id',
        'code',
        'parent_id',
        'title',
        'slug',
        'description',
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
        'hours' => 'integer',
        'max_students' => 'integer',
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'requirements' => 'array',
        'objectives' => 'array',
        'metadata' => 'array',
        'schedule' => 'array',
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
     * Get the parent course.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CampusCourse::class, 'parent_id');
    }

    /**
     * Get the child courses.
     */
    public function children(): HasMany
    {
        return $this->hasMany(CampusCourse::class, 'parent_id');
    }

    /**
     * Get the root parent course.
     */
    public function root(): BelongsTo
    {
        return $this->belongsTo(CampusCourse::class, 'parent_id')->with('parent');
    }

    /**
     * Check if this is a base course (no parent).
     */
    public function isBaseCourse(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this is an instance course (has parent).
     */
    public function isInstanceCourse(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Get all courses in the same family.
     */
    public function getFamilyAttribute(): Collection
    {
        if ($this->isBaseCourse()) {
            return $this->children;
        } else {
            return $this->parent->children;
        }
    }

    /**
     * Get instances of this course.
     */
    public function instances(): HasMany
    {
        return $this->hasMany(CampusCourse::class, 'parent_id');
    }

    /**
     * Get the appropriate code.
     */
    public function getEffectiveCodeAttribute(): string
    {
        return $this->code ?? '';
    }

    /**
     * Genera un codi automàtic per a un curs a partir del títol
     * 
     * @param string $title
     * @return string
     */
    public static function generateCourseCode($title)
    {
        // 1. Normalitzar text (accents i caràcters especials)
        $normalized = Str::ascii($title);
        $normalized = strtoupper($normalized);
        $normalized = preg_replace('/[^A-Z\s]/', '', $normalized);

        // 2. Separar paraules
        $words = array_values(array_filter(explode(' ', $normalized)));
        $count = count($words);

        $base = '';

        if ($count == 1) {
            $base = substr($words[0], 0, 6);
        }

        elseif ($count == 2) {
            $base =
                substr($words[0], 0, 3) .
                substr($words[1], 0, 3);
        }

        elseif ($count == 3) {
            foreach ($words as $w) {
                $base .= substr($w, 0, 2);
            }
        }

        elseif ($count == 4) {
            $base =
                substr($words[0], 0, 3) .
                substr($words[1], 0, 1) .
                substr($words[2], 0, 1) .
                substr($words[3], 0, 1);
        }

        elseif ($count == 5) {
            $base =
                substr($words[0], 0, 2) .
                substr($words[1], 0, 2) .
                substr($words[2], 0, 1) .
                substr($words[3], 0, 1);
        }

        else { // 6 o més
            foreach ($words as $w) {
                $base .= substr($w, 0, 1);
                if (strlen($base) >= 6) break;
            }
        }

        // Assegurar 6 caràcters
        $base = substr(str_pad($base, 6, 'X'), 0, 6);

        // 3. Generar número incremental
        $counter = 1;

        do {
            $code = $base . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $exists = CampusCourse::where('code', $code)->exists();
            $counter++;
        } while ($exists);

        return $code;
    }

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
     * Update status based on course completion.
     */
    public function updateStatus()
    {
        $requiredFields = [
            'space_id' => $this->space_id,
            'time_slot_id' => $this->time_slot_id,
            'semester' => $this->semester ?? null, // From form data
            'sessions' => $this->sessions,
            'hours' => $this->hours,
            'max_students' => $this->max_students,
        ];

        $filledFields = array_filter($requiredFields, function($value) {
            return !is_null($value) && $value !== '';
        });

        $completionPercentage = (count($filledFields) / count($requiredFields)) * 100;

        // Auto-update based on completion
        if ($completionPercentage >= 85) {
            // If course has space and time_slot assigned, mark as completed
            if ($this->space_id && $this->time_slot_id) {
                $this->status = self::STATUS_COMPLETED;
            } else {
                $this->status = self::STATUS_IN_PROGRESS;
            }
        } elseif ($completionPercentage >= 50) {
            $this->status = self::STATUS_PLANNING;
        } else {
            $this->status = self::STATUS_DRAFT;
        }

        $this->save();
    }

    /**
     * Mark course as completed when assigned to calendar.
     */
    public function markAsCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    /**
     * Check if space is occupied by any course.
     */
    public static function isSpaceOccupied($spaceId)
    {
        return self::where('space_id', $spaceId)
                   ->whereNotNull('time_slot_id')
                   ->where('status', '!=', self::STATUS_CLOSED)
                   ->exists();
    }

    /**
     * Get space availability status.
     */
    public static function getSpaceAvailability($spaceId)
    {
        $occupiedCourses = self::where('space_id', $spaceId)
                              ->whereNotNull('time_slot_id')
                              ->where('status', '!=', self::STATUS_CLOSED)
                              ->count();

        return [
            'occupied' => $occupiedCourses > 0,
            'courses_count' => $occupiedCourses,
            'status' => $occupiedCourses > 0 ? 'occupied' : 'available'
        ];
    }

    /**
     * Get status color for display.
     */
    public function getStatusColor()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_PLANNING => 'blue',
            self::STATUS_IN_PROGRESS => 'green',
            self::STATUS_COMPLETED => 'gray',
            self::STATUS_CLOSED => 'red',
            default => 'yellow'
        };
    }

    /**
     * Get status label in Catalan.
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Esborrany',
            self::STATUS_PLANNING => 'Planificació',
            self::STATUS_IN_PROGRESS => 'En curs',
            self::STATUS_COMPLETED => 'Assignat',
            self::STATUS_CLOSED => 'Tancat',
            default => $this->status
        };
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
        )->withPivot('role', 'sessions_assigned', 'assigned_at', 'finished_at', 'metadata')
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
            ->sum('sessions_assigned');
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
            ->withPivot(['role', 'sessions_assigned', 'assigned_at', 'finished_at'])
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