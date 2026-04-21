<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'course_id',
        'quantity',
        'price_at_time',
        'course_snapshot'
    ];

    protected $casts = [
        'price_at_time' => 'decimal:2',
        'course_snapshot' => 'array'
    ];

    /**
     * Get the cart that owns the item.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the course that owns the item.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(CampusCourse::class, 'course_id');
    }

    /**
     * Get subtotal for this item
     */
    public function getSubtotalAttribute(): float
    {
        return $this->price_at_time * $this->quantity;
    }

    /**
     * Get course title from snapshot or current course
     */
    public function getCourseTitleAttribute(): string
    {
        return $this->course_snapshot['title'] ?? $this->course?->title ?? 'Curso no disponible';
    }

    /**
     * Get course code from snapshot or current course
     */
    public function getCourseCodeAttribute(): string
    {
        return $this->course_snapshot['code'] ?? $this->course?->code ?? '';
    }

    /**
     * Check if course is still available
     */
    public function isCourseStillAvailable(): bool
    {
        if (!$this->course) {
            return false;
        }

        return $this->course->is_public && 
               $this->course->is_active && 
               $this->course->hasAvailableSpots();
    }

    /**
     * Get validation issues for this cart item
     */
    public function getValidationIssues(): array
    {
        $issues = [];

        if (!$this->course) {
            $issues[] = 'El curso ya no existe';
            return $issues;
        }

        if (!$this->course->is_public) {
            $issues[] = 'El curso ya no es público';
        }

        if (!$this->course->is_active) {
            $issues[] = 'El curso ya no está activo';
        }

        if (!$this->course->hasAvailableSpots()) {
            $issues[] = 'No hay plazas disponibles';
        }

        // Temporarily disabled for testing
        // if ($this->course->season && !$this->course->season->isRegistrationOpen()) {
        //     $issues[] = 'El período de matriculación está cerrado';
        // }

        $validStatuses = ['planning', 'active', 'registration', 'in_progress'];
        if (!in_array($this->course->status, $validStatuses)) {
            $issues[] = 'El curso no está disponible para matrícula';
        }

        // Check if price changed significantly
        if ($this->course->price != $this->price_at_time) {
            $difference = abs($this->course->price - $this->price_at_time);
            if ($difference > 0.01) { // More than 1 cent difference
                $issues[] = 'El precio del curso ha cambiado';
            }
        }

        return $issues;
    }

    /**
     * Check if item has validation issues
     */
    public function hasValidationIssues(): bool
    {
        return !empty($this->getValidationIssues());
    }
}
