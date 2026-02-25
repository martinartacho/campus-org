<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampusCourseSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'space_id',
        'time_slot_id',
        'semester',
        'status',
        'session_count',
        'start_date',
        'end_date',
        'notes'
    ];

    protected $casts = [
        'session_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public const STATUSES = [
        'assigned' => 'Assignat',
        'pending' => 'Pendent',
        'conflict' => 'Conflicte'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(CampusCourse::class, 'course_id');
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(CampusSpace::class, 'space_id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(CampusTimeSlot::class, 'time_slot_id');
    }

    public function detectConflicts(): array
    {
        $conflicts = [];
        
        // Check capacity
        if ($this->space && $this->course && $this->space->capacity < $this->course->max_students) {
            $conflicts[] = 'Capacidad insuficiente';
        }
        
        // Check teacher availability
        $teacher = $this->course->mainTeacher();
        if ($teacher) {
            $teacherSchedule = CampusTeacherSchedule::where('teacher_id', $teacher->id)
                ->where('time_slot_id', $this->time_slot_id)
                ->where('semester', $this->semester)
                ->first();
                
            if (!$teacherSchedule || !$teacherSchedule->is_available) {
                $conflicts[] = 'Profesor no disponible';
            }
        }
        
        return $conflicts;
    }
}
