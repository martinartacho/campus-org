<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampusTimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'code',
        'start_time',
        'end_time',
        'description',
        'is_active'
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean'
    ];

    public const DAYS = [
        1 => 'Dilluns',
        2 => 'Dimarts',
        3 => 'Dimecres',
        4 => 'Dijous',
        5 => 'Divendres'
    ];

    public const TIME_CODES = [
        'M11' => 'Matí 11:00-12:30',
        'T16' => 'Tarda 16:00-17:30',
        'T18' => 'Tarda 18:00-19:30'
    ];

    /**
     * Get the course schedules for this time slot.
     */
    public function courseSchedules(): HasMany
    {
        return $this->hasMany(CampusCourseSchedule::class, 'time_slot_id');
    }

    /**
     * Get the teacher schedules for this time slot.
     */
    public function teacherSchedules(): HasMany
    {
        return $this->hasMany(CampusTeacherSchedule::class, 'time_slot_id');
    }

    /**
     * Get formatted day name.
     */
    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? 'Desconegut';
    }

    /**
     * Get formatted time range.
     */
    public function getFormattedTimeAttribute(): string
    {
        return "{$this->start_time->format('H:i')} - {$this->end_time->format('H:i')}";
    }

    /**
     * Get full description with day and time.
     */
    public function getFullDescriptionAttribute(): string
    {
        return "{$this->day_name} - {$this->formatted_time}";
    }

    /**
     * Get available teachers for this time slot and semester.
     */
    public function getAvailableTeachers(string $semester)
    {
        return $this->teacherSchedules()
            ->where('semester', $semester)
            ->where('is_available', true)
            ->with('teacher')
            ->get()
            ->pluck('teacher');
    }

    /**
     * Check if time slot has conflicts for a semester.
     */
    public function hasConflicts(string $semester): bool
    {
        return $this->courseSchedules()
            ->where('semester', $semester)
            ->where('status', 'conflict')
            ->exists();
    }
}
