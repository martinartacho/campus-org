<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CampusCourseStudent extends Model
{
    use HasFactory;

    const STATUS_ENROLLED = 'enrolled';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DROPPED = 'dropped';
    const STATUS_TRANSFERRED = 'transferred';
    const STATUS_SUSPENDED = 'suspended';

    const GRADE_STATUS_PENDING = 'pending';
    const GRADE_STATUS_GRADED = 'graded';
    const GRADE_STATUS_APPEALED = 'appealed';
    const GRADE_STATUS_FINAL = 'final';

    const ATTENDANCE_REGULAR = 'regular';
    const ATTENDANCE_IRREGULAR = 'irregular';
    const ATTENDANCE_EXCELLENT = 'excellent';
    const ATTENDANCE_POOR = 'poor';

    protected $fillable = [
        'student_id',
        'course_id',
        'season_id',
        'enrollment_date',
        'academic_status',
        'start_date',
        'end_date',
        'completion_date',
        'final_grade',
        'grade_letter',
        'grade_status',
        'attendance_status',
        'attendance_percentage',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'completion_date' => 'date',
        'final_grade' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(CampusStudent::class, 'student_id');
    }

    /**
     * Get the course.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(CampusCourse::class, 'course_id');
    }

    /**
     * Get the season.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(CampusSeason::class, 'season_id');
    }

    /**
     * Scope for specific academic status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('academic_status', $status);
    }

    /**
     * Scope for active students.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('academic_status', [self::STATUS_ENROLLED, self::STATUS_ACTIVE]);
    }

    /**
     * Scope for completed students.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('academic_status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for specific season.
     */
    public function scopeInSeason(Builder $query, int $seasonId): Builder
    {
        return $query->where('season_id', $seasonId);
    }

    /**
     * Scope for specific course.
     */
    public function scopeInCourse(Builder $query, int $courseId): Builder
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope for graded students.
     */
    public function scopeGraded(Builder $query): Builder
    {
        return $query->whereNotNull('final_grade')
                    ->where('grade_status', '!=', self::GRADE_STATUS_PENDING);
    }

    /**
     * Get status label with color.
     */
    public function getStatusLabel(): array
    {
        return match($this->academic_status) {
            self::STATUS_ENROLLED => ['label' => 'Matriculat', 'color' => 'blue'],
            self::STATUS_ACTIVE => ['label' => 'Actiu', 'color' => 'green'],
            self::STATUS_COMPLETED => ['label' => 'Completat', 'color' => 'purple'],
            self::STATUS_DROPPED => ['label' => 'Abandonat', 'color' => 'red'],
            self::STATUS_TRANSFERRED => ['label' => 'Transferit', 'color' => 'yellow'],
            self::STATUS_SUSPENDED => ['label' => 'Suspès', 'color' => 'orange'],
            default => ['label' => 'Desconegut', 'color' => 'gray'],
        };
    }

    /**
     * Get grade status label.
     */
    public function getGradeStatusLabel(): string
    {
        return match($this->grade_status) {
            self::GRADE_STATUS_PENDING => 'Pendent',
            self::GRADE_STATUS_GRADED => 'Qualificat',
            self::GRADE_STATUS_APPEALED => 'Reclamat',
            self::GRADE_STATUS_FINAL => 'Final',
            default => 'Desconegut',
        };
    }

    /**
     * Get attendance status label.
     */
    public function getAttendanceStatusLabel(): string
    {
        return match($this->attendance_status) {
            self::ATTENDANCE_REGULAR => 'Regular',
            self::ATTENDANCE_IRREGULAR => 'Irregular',
            self::ATTENDANCE_EXCELLENT => 'Excel·lent',
            self::ATTENDANCE_POOR => 'Pobre',
            default => 'No definit',
        };
    }

    /**
     * Check if student is currently active.
     */
    public function isCurrentlyActive(): bool
    {
        return in_array($this->academic_status, [self::STATUS_ENROLLED, self::STATUS_ACTIVE]) &&
               (!$this->end_date || now()->lte($this->end_date));
    }

    /**
     * Mark student as completed.
     */
    public function markAsCompleted(float $grade = null, string $gradeLetter = null): void
    {
        $this->update([
            'academic_status' => self::STATUS_COMPLETED,
            'completion_date' => now(),
            'final_grade' => $grade,
            'grade_letter' => $gradeLetter,
            'grade_status' => $grade ? self::GRADE_STATUS_GRADED : self::GRADE_STATUS_PENDING,
        ]);
    }

    /**
     * Mark student as dropped.
     */
    public function markAsDropped(string $reason = null): void
    {
        $this->update([
            'academic_status' => self::STATUS_DROPPED,
            'end_date' => now(),
            'notes' => $reason ? $this->notes . "\n\nBaixa: $reason" : $this->notes,
        ]);
    }

    /**
     * Update attendance.
     */
    public function updateAttendance(float $percentage): void
    {
        $status = match(true) {
            $percentage >= 95 => self::ATTENDANCE_EXCELLENT,
            $percentage >= 80 => self::ATTENDANCE_REGULAR,
            $percentage >= 60 => self::ATTENDANCE_IRREGULAR,
            default => self::ATTENDANCE_POOR,
        };

        $this->update([
            'attendance_percentage' => $percentage,
            'attendance_status' => $status,
        ]);
    }
}
