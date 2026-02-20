<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampusTeacherSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'time_slot_id',
        'semester',
        'is_available',
        'preferences',
        'notes'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'preferences' => 'array'
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(CampusTeacher::class, 'teacher_id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(CampusTimeSlot::class, 'time_slot_id');
    }

    public function getPreferredSpacesAttribute(): array
    {
        return $this->preferences['preferred_spaces'] ?? [];
    }
}
