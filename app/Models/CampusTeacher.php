<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;

/**
 * App\Models\CampusTeacher
 *
 * @property int $id
 * @property int $user_id
 * @property string $teacher_code
 * @property string $first_name
 * @property string $last_name
 * @property string $dni
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $postal_code
 * @property string $city
 * @property string $iban
 * @property string $bank_titular
 * @property string $fiscal_id
 * @property string $fiscal_situation
 * @property string $invoice
 * @property string $degree
 * @property string $specialization
 * @property string $title
 * @property array $areas
 * @property string $status
 * @property \Illuminate\Support\Carbon $hiring_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CampusCourse[] $courses
 * @property-read int|null $courses_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\CampusTeacherFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereBankTitular($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereDni($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereFiscalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereFiscalSituation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereHiringDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereIban($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereSpecialization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereTeacherCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher withUnique()
 * @method static \Illuminate\Database\Eloquent\Builder|CampusTeacher withUniqueOr()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CampusTeacherPayment[] $payments
 * @property-read int|null $payments_count
 */
class CampusTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'dni',
        'email',
        'phone',
        'address',
        'postal_code',
        'city',
        'iban',
        'bank_titular',
        'fiscal_id',
        'fiscal_situation',
        'degree',
        'specialization',
        'needs_payment',
        'invoice',
        'title',
        'areas',
        'status',
        'observacions',
        'hiring_date',
        'metadata',
        // Camps de pagament
        'payment_type',
        'beneficiary_dni',
        'beneficiary_iban',
        'beneficiary_titular',
        'beneficiary_fiscal_situation',
        'beneficiary_invoice',
        'beneficiary_city',
        'beneficiary_postal_code',
        'waived_confirmation',
        'own_confirmation',
        'ceded_confirmation',
        'payment_status',
        'payment_confirmed_at',
        'payment_pdf_path',
    ];

    protected $casts = [
        'hiring_date' => 'date',
        'areas' => 'array',
        'metadata' => 'array',
        // 🔐 Dades sensibles xifrades (RGPD) - Accessors personalitzats
        // 'iban' => 'encrypted', // Desactivat per problema de serialització
        'bank_titular' => 'encrypted',
        'fiscal_id' => 'encrypted',
        'beneficiary_iban' => 'encrypted',
        'beneficiary_titular' => 'encrypted',
        'beneficiary_dni' => 'encrypted',
        // 🔐 Dates
        'payment_confirmed_at' => 'datetime',
        'waived_confirmation' => 'boolean',
        'own_confirmation' => 'boolean',
        'ceded_confirmation' => 'boolean',
        'beneficiary_invoice' => 'boolean',
    ];

    /**
     * Get IBAN attribute with manual decryption.
     */
    public function getIbanAttribute($value)
    {
        if (empty($value)) {
            return '';
        }
        
        try {
            return Crypt::decrypt($value);
        } catch (\Exception $e) {
            return '';
        }
    }
    
    /**
     * Set IBAN attribute with manual encryption.
     */
    public function setIbanAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['iban'] = null;
            return;
        }
        
        try {
            $this->attributes['iban'] = Crypt::encrypt($value);
        } catch (\Exception $e) {
            $this->attributes['iban'] = null;
        }
    }

    /**
     * Get the formatted IBAN attribute.
     */
    public function getFormattedIbanAttribute(): string
    {
        $iban = $this->iban;
        
        if (empty($iban)) {
            return '';
        }
        
        // Si está encriptado, Laravel lo desencripta automáticamente
        // Formatear para mostrar solo primeros y últimos dígitos
        $clean = str_replace(' ', '', $iban);
        
        if (strlen($clean) >= 24) {
            return substr($clean, 0, 4) . ' **** ' . substr($clean, -4);
        }
        
        return $iban;
    }
    
    /**
     * Get the masked IBAN attribute for display.
     */
    public function getMaskedIbanAttribute(): string
    {
        return $this->getFormattedIbanAttribute();
    }

    /**
     * Get the user that owns the teacher profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Format IBAN with spaces every 4 characters.
     */
    private function formatIban(string $iban): string
    {
        // Remove existing spaces
        $cleanIban = str_replace(' ', '', $iban);
        
        // Add spaces every 4 characters
        return implode(' ', str_split($cleanIban, 4));
    }

    /**
     * Get the courses taught by the teacher.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            CampusCourse::class,
            'campus_course_teacher',  // nom de la taula pivot
            'teacher_id',             // foreign key en la taula pivot per a CampusTeacher
            'course_id',              // foreign key en la taula pivot per a CampusCourse
            'id',                     // local key en la taula CampusTeacher
            'id'                      // local key en la taula CampusCourse
        )->withPivot('role', 'sessions_assigned', 'assigned_at', 'finished_at', 'metadata')
        ->withTimestamps();
        
    }


    /**
     * Get current courses (active and not finished).
     */
    public function currentCourses()
    {
        return $this->courses()
                    ->where('is_active', true)
                    ->where('end_date', '>=', now())
                    ->wherePivot('finished_at', null);
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        $fullName = "{$this->first_name} {$this->last_name}";
        
        if (!empty($this->title)) {
            $fullName = "{$this->title} {$fullName}";
        }
        
        return $fullName;
    }

    /**
     * Get formatted specialization.
     */
    public function getFormattedSpecializationAttribute(): ?string
    {
        if (empty($this->areas)) {
            return $this->specialization;
        }
        
        $areas = implode(', ', $this->areas);
        return $this->specialization 
            ? "{$this->specialization} ({$areas})"
            : $areas;
    }

    /**
     * Scope for active teachers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if teacher is currently teaching any course.
     */
    public function isCurrentlyTeaching(): bool
    {
        return $this->currentCourses()->exists();
    }

    /**
     * Get total sessions assigned across all courses.
     */
    public function getTotalAssignedSessionsAttribute(): float
    {
        return $this->courses()
                    ->wherePivot('finished_at', null)
                    ->sum('campus_course_teacher.sessions_assigned');
    }

    /**
     * Get the latest active course assignment.
     */
    public function currentCourse()
    {
        return $this->courses()
            ->wherePivotNull('finished_at')
            ->where('is_active', true)
            ->where('end_date', '>=', now())
            ->orderBy('campus_course_teacher.assigned_at', 'desc')
            ->first();
    }

    /**
     * Get detailed course assignments with season and category info.
     */
    public function detailedCourseAssignments()
    {
        return $this->courses()
            ->wherePivotNull('finished_at')
            ->with(['season', 'category'])
            ->select(
                'campus_courses.*',
                'campus_course_teacher.role',
                'campus_course_teacher.sessions_assigned',
                'campus_course_teacher.assigned_at',
                'campus_course_teacher.finished_at'
            )
            ->orderBy('campus_course_teacher.assigned_at', 'desc')
            ->get();
    }

    /**
     * Get courses grouped by role.
     */
    public function coursesByRole()
    {
        return $this->courses()
            ->wherePivotNull('finished_at')
            ->get()
            ->groupBy('pivot.role');
    }

    /**
     * Check if teacher has active course assignments.
     */
    public function hasActiveAssignments(): bool
    {
        return $this->courses()
            ->wherePivotNull('finished_at')
            ->exists();
    }

    /**
     * Get the payment data for this teacher.
     */
    public function payments()
    {
        return $this->hasMany(CampusTeacherPayment::class, 'teacher_id');
    }

    /**
     * Get payments for a specific season.
     */
    public function paymentsForSeason($seasonId)
    {
        return $this->payments()->where('season_id', $seasonId)->get();
    }

    /**
     * Get payments for a specific course.
     */
    public function paymentsForCourse($courseId)
    {
        return $this->payments()->where('course_id', $courseId)->get();
    }

}