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
        'is_active',
        'is_current',
        'periods'
    ];

    protected $casts = [
        'registration_start' => 'date',
        'registration_end' => 'date',
        'season_start' => 'date',
        'season_end' => 'date',
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

    public function teacherPayments()
    {
        return $this->hasMany(CampusTeacherPayment::class, 'season_id');
    }
}