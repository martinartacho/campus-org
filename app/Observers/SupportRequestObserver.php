<?php

namespace App\Observers;

use App\Models\SupportRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SupportRequestObserver
{
    /**
     * Handle the SupportRequest "created" event.
     */
    public function created(SupportRequest $supportRequest)
    {
        try {
            // Generate ticket number if not already set
            if (empty($supportRequest->ticket_number)) {
                $supportRequest->ticket_number = SupportRequest::generateTicketNumber($supportRequest);
                $supportRequest->save();
            }

            $ticketNumber = $supportRequest->ticket_number;

            // 1. Notificación al remitente (si existe en el sistema)
            $user = User::where('email', $supportRequest->email)->first();
            
            if ($user) {
                Notification::create([
                    'title' => "Confirmació Ticket #{$ticketNumber}",
                    'content' => $this->getConfirmationMessage($supportRequest, $ticketNumber),
                    'type' => 'support',
                    'sender_id' => auth()->id() ?? 1,
                    'recipient_type' => 'specific',
                    'recipient_ids' => [$user->id],
                    'is_published' => true,
                    'published_at' => now(),
                ]);
            }

            // 2. Notificación al departamento responsable
            $departmentRole = $this->getDepartmentRole($supportRequest->department);
            
            if ($departmentRole) {
                Notification::create([
                    'title' => "Nova Sol·licitud de Suport #{$ticketNumber}",
                    'content' => $this->getDepartmentNotificationMessage($supportRequest, $ticketNumber),
                    'type' => 'support',
                    'sender_id' => auth()->id() ?? 1,
                    'recipient_type' => 'role',
                    'recipient_role' => $departmentRole,
                    'is_published' => true,
                    'published_at' => now(),
                ]);
            }

            // 3. Notificación a administradores para seguimiento general
            Notification::create([
                'title' => "Seguiment: Sol·licitud #{$ticketNumber}",
                'content' => $this->getAdminNotificationMessage($supportRequest, $ticketNumber),
                'type' => 'support',
                'sender_id' => auth()->id() ?? 1,
                'recipient_type' => 'role',
                'recipient_role' => 'admin',
                'is_published' => true,
                'published_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Log error pero no interrumpir el flujo
            Log::error('Error sending support notification: ' . $e->getMessage());
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

Número de Ticket: {$ticketNumber}
Departament: {$this->getDepartmentName($supportRequest->department)}
Tipus: {$this->getTypeName($supportRequest->type)}
Urgència: {$this->getUrgencyName($supportRequest->urgency)}

Podeu fer seguiment del vostre cas a través del número de ticket.

Gràcies per contactar amb nosaltres.";
    }

    /**
     * Mensaje para el departamento
     */
    private function getDepartmentNotificationMessage($supportRequest, $ticketNumber)
    {
        return "Nova sol·licitud de suport rebuda:

Número de Ticket: {$ticketNumber}
Remitent: {$supportRequest->name} ({$supportRequest->email})
Departament: {$this->getDepartmentName($supportRequest->department)}
Tipus: {$this->getTypeName($supportRequest->type)}
Urgència: {$this->getUrgencyName($supportRequest->urgency)}

Descripció:
{$supportRequest->description}

Si us plau, reviseu aquesta sol·licitud i proporcioneu una resposta al més aviat possible.";
    }

    /**
     * Mensaje para administradores
     */
    private function getAdminNotificationMessage($supportRequest, $ticketNumber)
    {
        return "Seguiment de sol·licitud de suport:

Número de Ticket: {$ticketNumber}
Remitent: {$supportRequest->name} ({$supportRequest->email})
Departament: {$this->getDepartmentName($supportRequest->department)}
Estat: Pendent

Enllaç de gestió: " . url('/admin/support-requests/' . $supportRequest->id) . "
        ";
    }

    /**
     * Obtener nombre del departamento
     */
    private function getDepartmentName($department)
    {
        $names = [
            'admin' => 'Administració',
            'junta' => 'Junta Directiva',
            'manager' => 'Direcció',
            'coordinacio' => 'Coordinació',
            'gestio' => 'Gestió',
            'comunicacio' => 'Comunicació',
            'secretaria' => 'Secretaria',
            'editor' => 'Edició',
            'treasury' => 'Tresoreria',
            'general' => 'General'
        ];

        return $names[$department] ?? $department;
    }

    /**
     * Obtener nombre del tipo
     */
    private function getTypeName($type)
    {
        $names = [
            'service' => 'Nou servei',
            'incident' => 'Incidència',
            'improvement' => 'Millora',
            'consultation' => 'Consulta'
        ];

        return $names[$type] ?? $type;
    }

    /**
     * Obtener nombre de la urgencia
     */
    private function getUrgencyName($urgency)
    {
        $names = [
            'low' => 'Baixa',
            'medium' => 'Mitja',
            'high' => 'Alta',
            'critical' => 'Crítica'
        ];

        return $names[$urgency] ?? $urgency;
    }
}
