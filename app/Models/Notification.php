<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'type',
        'sender_id',
        'recipient_type',
        'recipient_role',
        'recipient_ids',
        'is_published',
        'published_at',
        'email_sent',
        'web_sent',
        'push_sent',
        'ticket_id',
        'template_type',
        'is_support_ticket'
    ];

    protected $casts = [
        'recipient_ids' => 'array',
        'published_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'notification_user')
                    ->withPivot(['read', 'read_at','email_sent', 'web_sent', 'push_sent'])
                    ->orderBy('notification_user.created_at', 'desc')
                    ->withTimestamps();
    }

    // Accessors for counts
    public function getEmailSentCountAttribute()
    {
        return $this->recipients()->wherePivot('email_sent', true)->count();
    }

    public function getWebSentCountAttribute()
    {
        return $this->recipients()->wherePivot('web_sent', true)->count();
    }

    public function getPushSentCountAttribute()
    {
        return $this->recipients()->wherePivot('push_sent', true)->count();
    }

    public function getEmailPendingCountAttribute()
    {
        return $this->recipients()->wherePivot('email_sent', false)->count();
    }

    public function getWebPendingCountAttribute()
    {
        return $this->recipients()->wherePivot('web_sent', false)->count();
    }

    public function getPushPendingCountAttribute()
    {
        return $this->recipients()->wherePivot('push_sent', false)->count();
    }

    public function getIsFullySentAttribute()
    {
        return !$this->email_pending_count && 
            !$this->web_pending_count && 
            !$this->push_pending_count;
    }

    public function scopeUnread($query, $userId)
    {
        return $query->whereHas('recipients', function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->where('read', false)
              ->orderBy('notification_user.created_at', 'desc');
        });
    }

    public function markAsRead($userId)
    {
        $this->recipients()->updateExistingPivot($userId, [
            'read' => true,
            'read_at' => now()
        ]);
    }

    public function isRead($user = null)
    {
        $user = $user ?: auth()->user();

        if ($this->relationLoaded('pivot')) {
            // Para relación muchos-a-muchos
            return $this->pivot->read_at !== null;
        }

        // Para sistema estándar
        return $this->read_at !== null;
    }

/*	public function users()
	{
	    return $this->belongsToMany(User::class)
	        ->withPivot(['read', 'read_at', 'push_sent'])
	        ->withTimestamps();
	}
*/

public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user')
            ->withPivot('read', 'read_at')
            ->withTimestamps();
    }


	public function notifications()
	{
	    return $this->belongsToMany(Notification::class)
        	->withPivot(['read', 'read_at']);
	}

    // Support ticket methods
    public function isSupportTicket(): bool
    {
        return $this->is_support_ticket ?? false;
    }

    public function generateTicketId(): string
    {
        $date = now()->format('Ymd');
        $sequence = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        return "ADM-{$date}-{$sequence}";
    }

    public function getTicketNumber(): string
    {
        return $this->ticket_id ?? '';
    }

    public function getFormattedTicketId(): string
    {
        if (!$this->isSupportTicket()) {
            return '';
        }

        $ticketId = $this->getTicketNumber();
        $senderName = $this->sender ? $this->sender->name : 'Sistema';
        $senderEmail = $this->sender ? $this->sender->email : 'system@upg.cat';
        $date = $this->published_at ? $this->published_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i');

        return "⚠️ **NOVA SOL·LICITUD DE SUPORT ASSIGNADA**

📋 **Detalls del Ticket:**
- **Número de Ticket:** {$ticketId}
- **Remitent:** {$senderName} ({$senderEmail})
- **Departament:** admin
- **Tipus:** consultation
- **Urgència:** medium
- **Data:** {$date}

📝 **Descripció:** 
{$this->content}

🎯 **Acció Requerida:**
Aquesta sol·licitud ha estat assignada al vostre departament. Si us plau, reviseu-la i proporcioneu una resposta al més aviat possible.

Podeu gestionar aquesta sol·licitud a través del sistema de notificaciones o contactar directament amb el remitent.
Destinataris: Usuaris amb rol: admin
Estat: Publicat el {$date}";
    }

}
