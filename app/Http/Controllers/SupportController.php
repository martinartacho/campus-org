<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function create(Request $request)
    {
        return view('support.form', [
            'user'   => auth()->user(),
            'module' => $request->get('module'),
            'url'    => url()->previous(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'type' => 'required|in:service,incident,improvement,consultation',
            'description' => 'required|string|min:10',
            'urgency' => 'required|in:low,medium,high,critical',
            'module' => 'nullable|string|max:255',
            'url' => 'nullable|url',
            'department' => 'nullable|string|max:255',
        ]);

        // Crear solicitud de soporte (el ticket_number se genera automáticamente)
        $supportRequest = SupportRequest::create([
            'user_id'    => auth()->id(),
            'name'       => $request->name,
            'email'      => $request->email,
            'department' => $request->department,
            'type'       => $request->type,
            'description'=> $request->description,
            'module'     => $request->module,
            'url'        => $request->url,
            'urgency'    => $request->urgency,
            'status'     => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $ticketNumber = $supportRequest->ticket_number;

        // Crear notificación automática para el remitente
        $this->sendSupportNotification($supportRequest, $ticketNumber);

        return back()->with('success', "Sol·licitud enviada correctament. Número de ticket: {$ticketNumber}");
    }

    
    /**
     * Enviar notificación automática al remitente y departamento
     */
    private function sendSupportNotification($supportRequest, $ticketNumber)
    {
        try {
            // 1. Notificación al remitente (si existe en el sistema)
            $user = User::where('email', $supportRequest->email)->first();
            
            if ($user) {
                $notification = Notification::create([
                    'title' => "Confirmación Ticket #{$ticketNumber}",
                    'content' => $this->getConfirmationMessage($supportRequest, $ticketNumber),
                    'type' => 'support',
                    'sender_id' => auth()->id() ?? 1,
                    'recipient_type' => 'specific',
                    'recipient_ids' => [$user->id],
                    'is_published' => true,
                    'published_at' => now(),
                ]);
                
                // Assign recipients and send channels
                $this->assignNotificationRecipients($notification, [$user->id]);
                $this->sendNotificationChannels($notification);
            }

            // 2. Notificación al departamento responsable
            $departmentRole = $this->getDepartmentRole($supportRequest->department);
            
            if ($departmentRole) {
                $notification = Notification::create([
                    'title' => "Nova Sol·licitud de Suport #{$ticketNumber}",
                    'content' => $this->getDepartmentNotificationMessage($supportRequest, $ticketNumber),
                    'type' => 'support',
                    'sender_id' => auth()->id() ?? 1,
                    'recipient_type' => 'role',
                    'recipient_role' => $departmentRole,
                    'is_published' => true,
                    'published_at' => now(),
                ]);
                
                // Assign recipients and send channels
                $this->assignNotificationRecipients($notification, []);
                $this->sendNotificationChannels($notification);
            }

            // 3. Notificación a administradores para seguimiento general
            $notification = Notification::create([
                'title' => "Seguiment: Sol·licitud #{$ticketNumber}",
                'content' => $this->getAdminNotificationMessage($supportRequest, $ticketNumber),
                'type' => 'support',
                'sender_id' => auth()->id() ?? 1,
                'recipient_type' => 'role',
                'recipient_role' => 'admin',
                'is_published' => true,
                'published_at' => now(),
            ]);
            
            // Assign recipients and send channels
            $this->assignNotificationRecipients($notification, []);
            $this->sendNotificationChannels($notification);

        } catch (\Exception $e) {
            // Log error pero no interrumpir el flujo
            \Log::error('Error sending support notification: ' . $e->getMessage());
        }
    }

    /**
     * Obtener el rol correspondiente al departamento
     */
    private function getDepartmentRole($department)
    {
        $departmentRoles = [
            'admin' => 'admin',
            'junta' => 'junta',
            'manager' => 'manager',
            'coordinacio' => 'coordinacio',
            'gestio' => 'gestio',
            'comunicacio' => 'comunicacio',
            'secretaria' => 'secretaria',
            'editor' => 'editor',
            'treasury' => 'treasury',
            'general' => 'admin', // General va a admin
        ];

        return $departmentRoles[$department] ?? 'admin';
    }

    /**
     * Mensaje de confirmación para el remitente
     */
    private function getConfirmationMessage($supportRequest, $ticketNumber)
    {
        return "Estimat/a {$supportRequest->name},

La seva sol·licitud de suport ha estat rebuda correctament.

📋 <strong>Detalls del Ticket:</strong>
<ul>
<li><strong>Número de Ticket:</strong> {$ticketNumber}</li>
<li><strong>Remitent:</strong> {$supportRequest->name} ({$supportRequest->email})</li>
<li><strong>Departament:</strong> {$supportRequest->department}</li>
<li><strong>Tipus:</strong> {$supportRequest->type}</li>
<li><strong>Urgència:</strong> {$supportRequest->urgency}</li>
<li><strong>Data:</strong> {$supportRequest->created_at->format('d/m/Y H:i')}</li>
</ul>


📝 <strong>Descripció:</strong> {$supportRequest->description}

Procedirem a revisar la seva sol·licitud i li respondrem el més aviat possible.

Per a qualsevol consulta, faci referència al número de ticket {$ticketNumber}.

Gràcies per la seva paciència.

Equip de Suport
AIEP Campus";
    }

    /**
     * Mensaje para el departamento responsable
     */
    private function getDepartmentNotificationMessage($supportRequest, $ticketNumber)
    {
        return "<strong>⚠️ NOVA SOL·LICITUD DE SUPORT ASSIGNADA</strong>

<strong>📋 <strong>Detalls del Ticket:</strong></strong>
<ul>
<li><strong>Número de Ticket:</strong> {$ticketNumber}</li>
<li><strong>Remitent:</strong> {$supportRequest->name} ({$supportRequest->email})</li>
<li><strong>Departament:</strong> {$supportRequest->department}</li>
<li><strong>Tipus:</strong> {$supportRequest->type}</li>
<li><strong>Urgència:</strong> {$supportRequest->urgency}</li>
<li><strong>Data:</strong> {$supportRequest->created_at->format('d/m/Y H:i')}</li>
</ul>

<strong>📝Descripció:</strong>
<p>{$supportRequest->description}</p>

<h5>🎯 <strong>Acció Requerida:</strong></h5>
<p>Aquesta sol·licitud ha estat assignada al vostre departament. Si us plau, reviseu-la i proporcioneu una resposta al més aviat possible.</p>

<p>Podeu gestionar aquesta sol·licitud a través del sistema de notificaciones o contactar directament amb el remitent.</p>";
    }

    /**
     * Mensaje para administradores
     */
    private function getAdminNotificationMessage($supportRequest, $ticketNumber)
    {
        return "📊 <strong>SEGUIMENT DE SOL·LICITUD DE SUPORT</strong>

<strong>📋 <strong>Detalls del Ticket:</strong></strong>
<ul>
<li><strong>Número de Ticket:</strong> {$ticketNumber}</li>
<li><strong>Remitent:</strong> {$supportRequest->name} ({$supportRequest->email})</li>
<li><strong>Departament:</strong> {$supportRequest->department}</li>
<li><strong>Tipus:</strong> {$supportRequest->type}</li>
<li><strong>Urgència:</strong> {$supportRequest->urgency}</li>
<li><strong>Data:</strong> {$supportRequest->created_at->format('d/m/Y H:i')}</li>
</ul>

<strong>📝 <strong>Descripció:</strong></strong>
<p>{$supportRequest->description}</p>

<strong>✅ <strong>Estat:</strong></strong> Notificacions enviades al remitent i al departament responsable.

<p>Aquesta sol·licitud està sent gestionada pel departament corresponent. Podeu fer seguiment a través del panell d'administració.</p>";
    }

    /**
     * Assign recipients to notification_user table
     */
    private function assignNotificationRecipients(Notification $notification, array $recipientIds)
    {
        if ($notification->recipient_type === 'specific') {
            $notification->recipients()->syncWithPivotValues($recipientIds, [
                'email_sent' => false,
                'web_sent' => false,
                'push_sent' => false,
                'read' => false,
            ]);
        } elseif ($notification->recipient_type === 'role') {
            // For role-based notifications, get all users with that role
            $roleUsers = User::role($notification->recipient_role)->pluck('id')->toArray();
            $notification->recipients()->syncWithPivotValues($roleUsers, [
                'email_sent' => false,
                'web_sent' => false,
                'push_sent' => false,
                'read' => false,
            ]);
        }
    }

    /**
     * Send notification through all channels (email, web, push)
     */
    private function sendNotificationChannels(Notification $notification)
    {
        try {
            // Send email channel
            $this->sendEmailChannel($notification);
            
            // Send web channel
            $this->sendWebChannel($notification);
            
        } catch (\Exception $e) {
            \Log::error('Error sending notification channels: ' . $e->getMessage());
        }
    }

    /**
     * Send email channel
     */
    private function sendEmailChannel(Notification $notification)
    {
        $recipients = $this->getNotificationRecipients($notification);
        
        foreach ($recipients as $user) {
            try {
                // Check user's email notification preference
                if (!$user->wantsEmailNotification($notification->type)) {
                    \Log::info("User {$user->email} has disabled {$notification->type} email notifications");
                    continue;
                }

                // Verify email is valid
                if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                // Use Mailable class
                $mail = new \App\Mail\NotificationMail($notification, $user);
                
                \Mail::to($user->email)->send($mail);
                
                // Mark as sent in pivot table
                $notification->recipients()->updateExistingPivot($user->id, [
                    'email_sent' => true,
                    'email_sent_at' => now(),
                ]);

            } catch (\Exception $e) {
                \Log::error("Error sending email to {$user->email}: " . $e->getMessage());
            }
        }
    }

    /**
     * Send web channel
     */
    private function sendWebChannel(Notification $notification)
    {
        $users = $this->getNotificationRecipients($notification);

        foreach ($users as $user) {
            // Check user's web notification preference
            if (!$user->wantsWebNotification($notification->type)) {
                \Log::info("User {$user->email} has disabled {$notification->type} web notifications");
                continue;
            }

            // Mark as web sent (notification is already available in web)
            $notification->recipients()->updateExistingPivot($user->id, [
                'web_sent' => true,
                'web_sent_at' => now(),
            ]);
        }
    }

    /**
     * Get notification recipients based on type
     */
    private function getNotificationRecipients(Notification $notification)
    {
        switch ($notification->recipient_type) {
            case 'specific':
                return User::whereIn('id', $notification->recipient_ids ?? [])->get();
                
            case 'role':
                return User::role($notification->recipient_role)->get();
                
            default:
                return collect();
        }
    }
}
