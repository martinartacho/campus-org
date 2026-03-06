<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CampusOrdreTemp extends Model
{
    use HasFactory;

    protected $table = 'campus_ordres_temp';
    
    public $timestamps = false; // No usamos created_at/updated_at

    protected $fillable = [
        'wp_first_name',
        'wp_last_name', 
        'wp_email',
        'wp_phone',
        'wp_item_name',
        'wp_code',
        'wp_status',
        'wp_quantity', // 1 = cobrat (pagat), altres valors = pendents
        'wp_price',
        'course_code',
        'course_id',
        'validation_status',
        'validation_notes',
        'metadata',
        'imported_at',
        'validated_at',
        'validated_by'
    ];

    protected $casts = [
        'wp_price' => 'decimal:2',
        'wp_quantity' => 'integer',
        'metadata' => 'array',
        'imported_at' => 'datetime',
        'validated_at' => 'datetime'
    ];

    /**
     * Get course that matches this ordre.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(CampusCourse::class, 'course_id');
    }

    /**
     * Get user who validated this ordre.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Scope for pending validation.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('validation_status', 'pending');
    }

    /**
     * Scope for matched orders.
     */
    public function scopeMatched(Builder $query): Builder
    {
        return $query->where('validation_status', 'matched');
    }

    /**
     * Scope for manual validation needed.
     */
    public function scopeManual(Builder $query): Builder
    {
        return $query->where('validation_status', 'manual');
    }

    /**
     * Scope for validation errors.
     */
    public function scopeError(Builder $query): Builder
    {
        return $query->where('validation_status', 'error');
    }

    /**
     * Check if ordre is validated.
     */
    public function isValidated(): bool
    {
        return !is_null($this->validated_at);
    }

    /**
     * Check if ordre has course match.
     */
    public function hasCourseMatch(): bool
    {
        return !is_null($this->course_id);
    }

    /**
     * Get payment status label.
     */
    public function getPaymentStatusLabelAttribute()
    {
        $quantity = (int) $this->wp_quantity;
        return $quantity === 1 ? 'Pagat' : 'Pendent';
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->wp_first_name} {$this->wp_last_name}");
    }

    /**
     * Get formatted validation status.
     */
    public function getFormattedValidationStatusAttribute(): string
    {
        $statuses = [
            'pending' => 'Pendent de validar',
            'matched' => 'Curs trobat',
            'manual' => 'Revisió manual',
            'error' => 'Error'
        ];
        
        return $statuses[$this->validation_status] ?? $this->validation_status;
    }

    /**
     * Check if ordre is paid (cobrat).
     */
    public function isPaid(): bool
    {
        return (int) $this->wp_quantity === 1;
    }

    /**
     * Get payment status label.
     */
    public function getPaymentStatusLabel(): string
    {
        return $this->isPaid() ? 'Cobrat' : 'Pendent';
    }

    /**
     * Auto-match course by code.
     */
    public function autoMatchCourse(): bool
    {
        $course = CampusCourse::where('code', $this->wp_code)->first();
        
        if ($course) {
            $this->course_id = $course->id;
            $this->course_code = $course->code;
            $this->validation_status = 'matched';
            $this->validation_notes = "Auto-matched: {$course->title}";
            $this->save(); // FALTAVA AQUESTA LÍNIA!
            return true;
        }
        
        return false;
    }

    /**
     * Try to match by title similarity.
     */
    public function matchByTitle(): bool
    {
        $courses = CampusCourse::all();
        
        foreach ($courses as $course) {
            similar_text(strtolower($this->wp_item_name), strtolower($course->title), $percent);
            
            if ($percent > 80) {
                $this->course_id = $course->id;
                $this->course_code = $course->code;
                $this->validation_status = 'matched';
                $this->validation_notes = "Title match ({$percent}%): {$course->title}";
                $this->save(); // FALTAVA AQUESTA LÍNIA!
                return true;
            }
        }
        
        return false;
    }
}
