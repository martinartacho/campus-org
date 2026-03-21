<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'department',
        'type',
        'description',
        'module',
        'url',
        'urgency',
        'user_id',
        'status',
        'ip_address',
        'user_agent',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
        'ticket_number',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function booted()
    {
        static::creating(function ($supportRequest) {
            // Generate ticket number if not provided
            if (empty($supportRequest->ticket_number)) {
                $supportRequest->ticket_number = static::generateTicketNumber($supportRequest);
            }
        });
    }

    /**
     * Generate ticket number with department abbreviation
     */
    public static function generateTicketNumber($supportRequest)
    {
        $departmentAbbreviations = [
            'admin' => 'ADM',
            'junta' => 'JNT',
            'manager' => 'MGR',
            'coordinacio' => 'COR',
            'gestio' => 'GST',
            'comunicacio' => 'COM',
            'secretaria' => 'SEC',
            'editor' => 'EDT',
            'treasury' => 'TRS',
            'general' => 'GEN',
        ];

        $department = strtolower($supportRequest->department ?? 'general');
        $abbr = $departmentAbbreviations[$department] ?? 'GEN';
        
        // Get next sequence for today
        $today = now()->format('Ymd');
        $lastTicket = static::where('ticket_number', 'like', $abbr . '-' . $today . '-%')
            ->orderBy('ticket_number', 'desc')
            ->first();
        
        if ($lastTicket) {
            $lastSequence = explode('-', $lastTicket->ticket_number)[2];
            $sequence = str_pad((int)$lastSequence + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $sequence = '00001';
        }
        
        return "{$abbr}-{$today}-{$sequence}";
    }

    /**
     * Tipos de solicitudes
     */
    const TYPES = [
        'service' => 'Nou servei',
        'incident' => 'Incidència',
        'improvement' => 'Millora',
        'consultation' => 'Consulta',
    ];

    /**
     * Niveles de urgencia
     */
    const URGENCY_LEVELS = [
        'low' => 'Baixa',
        'medium' => 'Mitjana',
        'high' => 'Alta',
        'critical' => 'Crítica',
    ];

    /**
     * Estados de la solicitud
     */
    const STATUSES = [
        'pending' => 'Pendent',
        'in_progress' => 'En procés',
        'resolved' => 'Resolta',
        'closed' => 'Tancada',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el usuario que resolvió la solicitud
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Obtener el tipo formateado
     */
    public function getTypeFormattedAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Obtener la urgencia formateada
     */
    public function getUrgencyFormattedAttribute(): string
    {
        return self::URGENCY_LEVELS[$this->urgency] ?? $this->urgency;
    }

    /**
     * Obtener el estado formateado
     */
    public function getStatusFormattedAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Obtener el color de urgencia para CSS
     */
    public function getUrgencyColorAttribute(): string
    {
        return match($this->urgency) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Obtener el color de estado para CSS
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'in_progress' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Scope para solicitudes pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para solicitudes por urgencia
     */
    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency', $urgency);
    }

    /**
     * Get type label in Catalan
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'service' => '🚀 Nou servei',
            'incident' => '⚠️ Incidència',
            'improvement' => '💡 Millora',
            'consultation' => '❓ Consulta',
            default => $this->type,
        };
    }

    /**
     * Get urgency label in Catalan
     */
    public function getUrgencyLabelAttribute()
    {
        return match($this->urgency) {
            'low' => 'Baixa',
            'medium' => 'Mitjana',
            'high' => 'Alta',
            'critical' => 'Crítica',
            default => $this->urgency,
        };
    }

    /**
     * Get status label in Catalan
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pendent',
            'in_progress' => 'En procés',
            'resolved' => 'Resolt',
            'closed' => 'Tancat',
            default => $this->status,
        };
    }

    /**
     * Scope para solicitudes de un usuario
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
