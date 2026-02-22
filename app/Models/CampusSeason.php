<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class CampusSeason extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'academic_year',
        'registration_start',
        'registration_end',
        'season_start',
        'season_end',
        'type',
        'status', // Nuevo campo
        'is_active',
        'is_current',
        'periods'
    ];

    protected $casts = [
        'registration_start' => 'date',
        'registration_end' => 'date',
        'season_start' => 'date',
        'season_end' => 'date',
        'status' => 'string',
        'is_active' => 'boolean',
        'is_current' => 'boolean',
        'periods' => 'array'
    ];

    /**
     * Get the courses for the season.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(CampusCourse::class, 'season_id');
    }

    /**
     * Scope for active seasons.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for current season.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope for seasons with specific status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for visible seasons (not draft).
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereIn('status', ['planning', 'active', 'registration', 'in_progress', 'completed']);
    }

    /**
     * Scope for manageable seasons (admin/manager can edit).
     */
    public function scopeManageable(Builder $query): Builder
    {
        return $query->whereIn('status', ['draft', 'planning', 'active', 'registration', 'in_progress']);
    }

    /**
     * Scope for seasons with open registration.
     */
    public function scopeWithOpenRegistration(Builder $query): Builder
    {
        return $query->where('registration_start', '<=', now())
                    ->where('registration_end', '>=', now());
    }

    /**
     * Check if registration is open.
     */
    public function isRegistrationOpen(): bool
    {
        return now()->between($this->registration_start, $this->registration_end);
    }

    /**
     * Check if season is in progress.
     */
    public function isInProgress(): bool
    {
        return now()->between($this->season_start, $this->season_end);
    }

    /**
     * Check if season is manageable (can be edited by admin/manager).
     */
    public function isManageable(): bool
    {
        return in_array($this->status, ['draft', 'planning', 'active', 'registration', 'in_progress']);
    }

    /**
     * Check if season is visible (can be seen by teachers/students).
     */
    public function isVisible(): bool
    {
        return in_array($this->status, ['planning', 'active', 'registration', 'in_progress', 'completed']);
    }

    /**
     * Get status label with color.
     */
    public function getStatusLabel(): array
    {
        return match($this->status) {
            'draft' => ['label' => 'Borrador', 'color' => 'gray'],
            'planning' => ['label' => 'Planificación', 'color' => 'blue'],
            'active' => ['label' => 'Activa', 'color' => 'green'],
            'registration' => ['label' => 'Inscripciones', 'color' => 'yellow'],
            'in_progress' => ['label' => 'En Curso', 'color' => 'blue'],
            'completed' => ['label' => 'Completada', 'color' => 'purple'],
            'archived' => ['label' => 'Archivada', 'color' => 'gray'],
            default => ['label' => 'Desconocido', 'color' => 'red'],
        };
    }

    public function teacherPayments()
    {
        return $this->hasMany(CampusTeacherPayment::class, 'season_id');
    }
}