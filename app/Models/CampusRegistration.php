<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CampusRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'season_id',
        'registration_code',
        'registration_date',
        'status',
        'amount',
        'payment_status',
        'payment_due_date',
        'payment_history',
        'payment_method',
        'grade',
        'attendance_status',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'registration_date' => 'date',
        'amount' => 'decimal:2',
        'payment_due_date' => 'date',
        'grade' => 'decimal:2',
        'payment_history' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Get the student that owns the registration.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(CampusStudent::class, 'student_id');
    }

    /**
     * Get the course that owns the registration.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(CampusCourse::class, 'course_id');
    }

    /**
     * Get the season that owns the registration.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(CampusSeason::class, 'season_id');
    }

    /**
     * Scope for confirmed registrations.
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for completed registrations.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending registrations.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid registrations.
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Check if registration is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if payment is complete.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Get formatted status.
     */
    public function getFormattedStatusAttribute(): string
    {
        $statuses = [
            'pending' => 'Pendent',
            'confirmed' => 'Confirmat',
            'cancelled' => 'Cancel·lat',
            'completed' => 'Completat',
            'failed' => 'Fallit'
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get formatted payment status.
     */
    public function getFormattedPaymentStatusAttribute(): string
    {
        $statuses = [
            'pending' => 'Pendent de pagament',
            'paid' => 'Pagat',
            'partial' => 'Pagament parcial',
            'cancelled' => 'Pagament cancel·lat'
        ];
        
        return $statuses[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Add payment to history.
     */
    public function addPayment($amount, $method, $reference = null, $notes = null): void
    {
        $payment = [
            'date' => now()->toISOString(),
            'amount' => $amount,
            'method' => $method,
            'reference' => $reference,
            'notes' => $notes
        ];
        
        $history = $this->payment_history ?? [];
        $history[] = $payment;
        
        $this->payment_history = $history;
        
        // Update payment status
        $totalPaid = collect($history)->sum('amount');
        if ($totalPaid >= $this->amount) {
            $this->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->payment_status = 'partial';
        }
        
        $this->save();
    }

    /**
     * Get total paid amount.
     */
    public function getTotalPaidAttribute(): float
    {
        if (empty($this->payment_history)) {
            return 0;
        }
        
        return collect($this->payment_history)->sum('amount');
    }

    /**
     * Get remaining amount to pay.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount - $this->total_paid);
    }

    /**
     * Create from WP ordre.
     */
    public static function createFromOrdreTemp(CampusOrdreTemp $ordre, int $studentId, int $seasonId): self
    {
        return self::create([
            'student_id' => $studentId,
            'season_id' => $seasonId,
            'course_id' => $ordre->course_id,
            'registration_code' => 'REG-' . date('Y') . '-' . str_pad($studentId, 4, '0', STR_PAD_LEFT) . '-' . str_pad($ordre->course_id, 4, '0', STR_PAD_LEFT),
            'registration_date' => now(),
            'amount' => $ordre->wp_price ?? 0,
            'payment_status' => $ordre->wp_quantity > 0 ? 'paid' : 'pending',
            'payment_history' => $ordre->wp_quantity > 0 ? [
                [
                    'date' => now()->toDateTimeString(),
                    'amount' => $ordre->wp_price ?? 0,
                    'method' => 'wordpress',
                    'reference' => $ordre->wp_code,
                    'notes' => 'Importat des de WordPress'
                ]
            ] : [],
            'status' => 'confirmed', // Canviat de 'active' a 'confirmed'
            'metadata' => [
                'source' => 'wordpress_import',
                'wp_ordre_id' => $ordre->id,
                'wp_code' => $ordre->wp_code,
                'wp_status' => $ordre->wp_status,
                'imported_at' => now()->toDateTimeString()
            ]
        ]);
    }
}