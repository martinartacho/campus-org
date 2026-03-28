<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherNotification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'type',
        'sender_id',
        'course_id',
        'recipient_type', // 'all' o 'specific'
        'recipient_ids', // array de student IDs
        'is_published',
        'published_at',
        'email_sent',
        'web_sent',
        'push_sent'
    ];

    protected $casts = [
        'recipient_ids' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Boot model
     */
    protected static function booted()
    {
        static::creating(function ($notification) {
            // Generate notification number if not provided
            if (empty($notification->notification_number)) {
                $notification->notification_number = static::generateNotificationNumber($notification);
            }
        });
    }

    /**
     * Generate notification number
     */
    public static function generateNotificationNumber($notification)
    {
        $today = now()->format('Ymd');
        $lastNotification = static::where('notification_number', 'like', 'TCH-' . $today . '-%')
            ->orderBy('notification_number', 'desc')
            ->first();
        
        if ($lastNotification) {
            $lastSequence = explode('-', $lastNotification->notification_number)[2];
            $sequence = str_pad((int)$lastSequence + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $sequence = '00001';
        }
        
        return "TCH-{$today}-{$sequence}";
    }

    /**
     * Tipos de notificaciones
     */
    const TYPES = [
        'general' => 'General',
        'reminder' => 'Recordatorio',
        'announcement' => 'Anuncio',
        'assignment' => 'Tarea',
        'grade' => 'Calificación',
        'emergency' => 'Urgente',
    ];

    /**
     * Estados de la notificación
     */
    const STATUSES = [
        'draft' => 'Borrador',
        'published' => 'Publicada',
        'scheduled' => 'Programada',
    ];

    /**
     * Relación con el usuario que envía
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relación con el curso
     */
    public function course()
    {
        return $this->belongsTo(CampusCourse::class, 'course_id');
    }

    /**
     * Relación con los destinatarios
     */
    public function recipients()
    {
        return $this->belongsToMany(User::class, 'teacher_notification_user')
                    ->withPivot(['read', 'read_at','email_sent', 'web_sent', 'push_sent'])
                    ->orderBy('teacher_notification_user.created_at', 'desc')
                    ->withTimestamps();
    }

    /**
     * Obtener el tipo formateado
     */
    public function getTypeFormattedAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Obtener el color de tipo para CSS
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'general' => 'primary',
            'reminder' => 'warning',
            'announcement' => 'info',
            'assignment' => 'success',
            'grade' => 'danger',
            'emergency' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get type label in Catalan
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'general' => '📢 General',
            'reminder' => '⏰ Recordatorio',
            'announcement' => '📣 Anuncio',
            'assignment' => '📝 Tarea',
            'grade' => '📊 Calificación',
            'emergency' => '🚨 Urgente',
            default => $this->type,
        };
    }

    /**
     * Scope para notificaciones publicadas
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope para notificaciones de un curso
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope para notificaciones de un teacher
     */
    public function scopeFromTeacher($query, $teacherId)
    {
        return $query->where('sender_id', $teacherId);
    }
}
