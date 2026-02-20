<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampusSpace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'capacity',
        'type',
        'description',
        'equipment',
        'is_active'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'equipment' => 'array',
        'is_active' => 'boolean'
    ];

    public const TYPES = [
        'sala_actes' => 'Sala d\'actes',
        'mitjana' => 'Aula mitjana',
        'petita' => 'Aula petita',
        'polivalent' => 'Sala polivalent',
        'extern' => 'Espai extern'
    ];

    /**
     * Get the course schedules for this space.
     */
    public function courseSchedules(): HasMany
    {
        return $this->hasMany(CampusCourseSchedule::class, 'space_id');
    }

    /**
     * Check if space is available for a specific time slot and semester.
     */
    public function isAvailable(int $timeSlotId, string $semester): bool
    {
        return !$this->courseSchedules()
            ->where('time_slot_id', $timeSlotId)
            ->where('semester', $semester)
            ->exists();
    }

    /**
     * Get formatted capacity with students.
     */
    public function getFormattedCapacityAttribute(): string
    {
        return "{$this->capacity} alumnes";
    }

    /**
     * Get formatted equipment list.
     */
    public function getFormattedEquipmentAttribute(): string
    {
        if (empty($this->equipment)) {
            return '—';
        }

        return collect($this->equipment)->map(function ($item) {
            return match($item) {
                'projector' => 'Projector',
                'tv' => 'TV',
                'audio' => 'Àudio',
                'ordinadors' => 'Ordinadors',
                default => $item
            };
        })->implode(', ');
    }
}
