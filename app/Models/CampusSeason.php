<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CampusSeason extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'academic_year',
        'parent_id',
        'type',
        'semester_number',
        'registration_start',
        'registration_end',
        'season_start',
        'season_end',
        'status',
        'is_active',
        'is_current',
        'periods',
        'requirements',
        'objectives',
        'metadata',
        'created_by',
        'source',
    ];

    protected $casts = [
        'registration_start' => 'date',
        'registration_end' => 'date',
        'season_start' => 'date',
        'season_end' => 'date',
        'is_active' => 'boolean',
        'is_current' => 'boolean',
        'periods' => 'array',
        'requirements' => 'array',
        'objectives' => 'array',
        'metadata' => 'array',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(CampusCourse::class, 'season_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CampusRegistration::class);
    }

    /**
     * Establece esta temporada como la actual, asegurando que solo una temporada esté activa
     */
    public function setAsCurrent(): void
    {
        // Desactivar todas las demás temporadas
        static::where('id', '!=', $this->id)->update(['is_current' => false]);
        
        // Activar esta temporada
        $this->update(['is_current' => true]);
    }

    /**
     * Obtiene la temporada actual (la que tiene is_current = 1)
     */
    public static function getCurrent(): ?self
    {
        return static::where('is_current', true)->first();
    }

    /**
     * Obtiene temporadas visibles según el usuario y permisos
     */
    public static function getVisibleForUser($user = null): Builder
    {
        $query = static::query();
        
        if (!$user) {
            $user = auth()->user();
        }
        
        // Si es admin o tiene permisos especiales, ve todas
        if ($user && ($user->hasRole('admin') || $user->can('manage_seasons'))) {
            return $query;
        }
        
        // Para otros roles, aplicar reglas de visibilidad
        return $query->where(function($q) {
            // Temporada actual siempre visible
            $q->where('is_current', true)
              // Temporadas pasadas con status 'completed'
              ->orWhere(function($subQ) {
                  $subQ->where('season_end', '<', now())
                       ->where('status', 'completed');
              })
              // Temporadas futuras no visibles para roles básicos
              ->where('season_start', '<=', now());
        });
    }

    /**
     * Verifica si la temporada es futura
     */
    public function isFuture(): bool
    {
        return $this->season_start > now();
    }

    /**
     * Verifica si la temporada es pasada
     */
    public function isPast(): bool
    {
        return $this->season_end < now();
    }

    /**
     * Verifica si la temporada es actual
     */
    public function isPresent(): bool
    {
        return !$this->isFuture() && !$this->isPast();
    }

    /**
     * Obtiene la temporada en planning
     */
    public static function getPlanning(): ?self
    {
        return static::where('status', 'planning')->first();
    }

    /**
     * Scope para obtener temporadas por status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Obtiene la temporada por defecto para el calendario
     * Prioridad: 1. Planning, 2. Current, 3. Primera activa
     */
    public static function getDefaultForCalendar(): ?self
    {
        // 1. Buscar temporada en planning
        $planning = static::getPlanning();
        if ($planning) {
            return $planning;
        }

        // 2. Buscar temporada actual
        $current = static::getCurrent();
        if ($current) {
            return $current;
        }

        // 3. Buscar primera temporada activa
        return static::where('is_active', true)->first();
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

    /**
     * Get the parent season (academic year).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CampusSeason::class, 'parent_id');
    }

    /**
     * Get the child seasons (semesters/trimesters).
     */
    public function children(): HasMany
    {
        return $this->hasMany(CampusSeason::class, 'parent_id')
                   ->orderBy('semester_number');
    }

    /**
     * Get academic years only (parent seasons).
     */
    public function scopeAcademicYears(Builder $query): Builder
    {
        return $query->where('type', 'annual')->whereNull('parent_id');
    }

    /**
     * Get semesters only.
     */
    public function scopeSemesters(Builder $query): Builder
    {
        return $query->where('type', 'semester')->whereNotNull('parent_id');
    }

    /**
     * Get first semester.
     */
    public function firstSemester()
    {
        return $this->children()->where('semester_number', 1)->first();
    }

    /**
     * Get second semester.
     */
    public function secondSemester()
    {
        return $this->children()->where('semester_number', 2)->first();
    }

    /**
     * Check if this is an academic year.
     */
    public function isAcademicYear(): bool
    {
        return $this->type === 'annual' && is_null($this->parent_id);
    }

    /**
     * Check if this is a semester.
     */
    public function isSemester(): bool
    {
        return $this->type === 'semester' && !is_null($this->parent_id);
    }

    /**
     * Get the academic year for this semester.
     */
    public function getAcademicYear()
    {
        if ($this->isAcademicYear()) {
            return $this;
        }
        
        return $this->parent;
    }

    /**
     * Get all courses in this season and its children.
     */
    public function getAllCourses()
    {
        if ($this->isAcademicYear()) {
            // Get courses from all semesters
            $seasonIds = $this->children()->pluck('id')->push($this->id);
            return CampusCourse::whereIn('season_id', $seasonIds)->get();
        } else {
            // Get courses from this semester only
            return $this->courses;
        }
    }
}