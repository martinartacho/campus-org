<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CampusCourseStudent extends Model
{
    use HasFactory;

    protected $table = 'campus_course_student'; // Forçar nom singular

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

    /**
     * Create from WP ordre.
     */
    public static function createFromOrdreTemp(CampusOrdreTemp $ordre, int $seasonId): self
    {
        // Find or create student
        $student = CampusStudent::firstOrCreate(
            ['email' => $ordre->wp_email],
            [
                'user_id' => User::firstOrCreate(['email' => $ordre->wp_email])->id,
                'student_code' => self::generateStudentCode($ordre->wp_first_name, $ordre->wp_last_name),
                'first_name' => $ordre->wp_first_name,
                'last_name' => $ordre->wp_last_name,
                'phone' => $ordre->wp_phone,
                'email' => $ordre->wp_email,
                'status' => 'active',
                'enrollment_date' => now(),
            ]
        );

        // Crear campus_registration també
        self::createRegistrationFromOrdre($ordre, $student->id);

        return self::create([
            'student_id' => $student->id,
            'course_id' => $ordre->course_id,
            'season_id' => $seasonId,
            'enrollment_date' => now(),
            'academic_status' => self::STATUS_ENROLLED,
            'start_date' => now(),
            'metadata' => [
                'source' => 'wp_ordre',
                'wp_ordre_id' => $ordre->id,
                'wp_code' => $ordre->wp_code,
                'wp_status' => $ordre->wp_status,
                'wp_price' => $ordre->wp_price,
                'wp_quantity' => $ordre->wp_quantity,
                'imported_at' => now()->toISOString(),
            ]
        ]);
    }

    /**
     * Crear campus_registration a partir de WP ordre.
     */
    private static function createRegistrationFromOrdre(CampusOrdreTemp $ordre, int $studentId): void
    {
        // Determinar estat segons Quantity
        $quantity = (int) $ordre->wp_quantity;
        $status = $quantity === 1 ? 'completed' : 'pending';
        $paymentStatus = $quantity === 1 ? 'paid' : 'pending';

        CampusRegistration::create([
            'student_id' => $studentId,
            'course_id' => $ordre->course_id,
            'season_id' => CampusSeason::where('is_current', true)->first()?->id ?? 1,
            'registration_code' => self::generateRegistrationCode(),
            'registration_date' => now(),
            'status' => $status,
            'amount' => $ordre->wp_price ?? 0,
            'payment_status' => $paymentStatus,
            'payment_method' => 'wordpress',
            'metadata' => [
                'source' => 'wp_ordre',
                'wp_ordre_id' => $ordre->id,
                'wp_quantity' => $ordre->wp_quantity,
                'imported_at' => now()->toISOString(),
            ]
        ]);
    }

    /**
     * Generate registration code.
     */
    private static function generateRegistrationCode(): string
    {
        do {
            $code = 'REG-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (CampusRegistration::where('registration_code', $code)->exists());
        
        return $code;
    }

    /**
     * Generate student code.
     */
    private static function generateStudentCode(string $firstName, string $lastName): string
    {
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $code = $initials . $random;

        // Ensure uniqueness
        while (CampusStudent::where('student_code', $code)->exists()) {
            $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $code = $initials . $random;
        }

        return $code;
    }

    /**
     * Check if student exists in course.
     */
    public static function existsInCourse(int $studentId, int $courseId, int $seasonId): bool
    {
        return self::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('season_id', $seasonId)
            ->exists();
    }
}
