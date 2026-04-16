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
        'user_id',
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
     * Get the user who created/updated the registration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
            'pending' => __('campus.registration_status_pending'),
            'confirmed' => __('campus.registration_status_confirmed'),
            'cancelled' => __('campus.registration_status_cancelled'),
            'completed' => __('campus.registration_status_completed'),
            'failed' => __('campus.registration_status_failed')
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get formatted payment status.
     */
    public function getFormattedPaymentStatusAttribute(): string
    {
        $statuses = [
            'pending' => __('campus.payment_status_pending'),
            'paid' => __('campus.payment_status_paid'),
            'partial' => __('campus.payment_status_partial'),
            'cancelled' => __('campus.payment_status_cancelled')
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
}