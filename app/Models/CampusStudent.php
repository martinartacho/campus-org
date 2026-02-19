<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class CampusStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_code',
        'first_name',
        'last_name',
        'dni',
        'birth_date',
        'phone',
        'address',
        'email',
        'emergency_contact',
        'emergency_phone',
        'status',
        'enrollment_date',
        'academic_record',
        'metadata'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'enrollment_date' => 'date',
        'academic_record' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Get the user that owns the student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the registrations for the student.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(CampusRegistration::class, 'student_id');
    }

    /**
     * Get the courses enrolled by the student.
     */
    public function courses()
    {
        return $this->hasManyThrough(
            CampusCourse::class,
            CampusRegistration::class,
            'student_id',   // Foreign key on CampusRegistration table
            'id',           // Foreign key on CampusCourse table
            'id',           // Local key on CampusStudent table
            'course_id'     // Local key on CampusRegistration table
        );
    }

    /**
     * Get active registrations.
     */
    public function activeRegistrations()
    {
        return $this->registrations()->whereIn('status', ['confirmed', 'completed']);
    }

    /**
     * Get completed courses.
     */
    public function completedCourses()
    {
        return $this->registrations()
                    ->where('status', 'completed')
                    ->with('course')
                    ->get()
                    ->pluck('course');
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Scope for active students.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for graduated students.
     */
    public function scopeGraduated(Builder $query): Builder
    {
        return $query->where('status', 'graduated');
    }

    /**
     * Check if student is currently enrolled in any course.
     */
    public function isCurrentlyEnrolled(): bool
    {
        return $this->activeRegistrations()
                    ->whereHas('course', function ($query) {
                        $query->where('is_active', true)
                              ->where('start_date', '<=', now())
                              ->where('end_date', '>=', now());
                    })
                    ->exists();
    }
}