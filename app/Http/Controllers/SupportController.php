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
                Notification::create([
                    'title' => "Confirmación Ticket #{$ticketNumber}",
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

📋 **Detalls del Ticket:**
- **Número de Ticket:** {$ticketNumber}
- **Tipus:** {$supportRequest->type}
- **Urgència:** {$supportRequest->urgency}
- **Departament:** {$supportRequest->department}
- **Data:** {$supportRequest->created_at->format('d/m/Y H:i')}

📝 **Descripció:** {$supportRequest->description}

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
        return "⚠️ **NOVA SOL·LICITUD DE SUPORT ASSIGNADA**

📋 **Detalls del Ticket:**
- **Número de Ticket:** {$ticketNumber}
- **Remitent:** {$supportRequest->name} ({$supportRequest->email})
- **Departament:** {$supportRequest->department}
- **Tipus:** {$supportRequest->type}
- **Urgència:** {$supportRequest->urgency}
- **Data:** {$supportRequest->created_at->format('d/m/Y H:i')}

📝 **Descripció:** {$supportRequest->description}

🎯 **Acció Requerida:**
Aquesta sol·licitud ha estat assignada al vostre departament. Si us plau, reviseu-la i proporcioneu una resposta al més aviat possible.

Podeu gestionar aquesta sol·licitud a través del sistema de notificaciones o contactar directament amb el remitent.";
    }

    /**
     * Mensaje para administradores
     */
    private function getAdminNotificationMessage($supportRequest, $ticketNumber)
    {
        return "📊 **SEGUIMENT DE SOL·LICITUD DE SUPORT**

📋 **Detalls del Ticket:**
- **Número de Ticket:** {$ticketNumber}
- **Remitent:** {$supportRequest->name} ({$supportRequest->email})
- **Departament Assignat:** {$supportRequest->department}
- **Tipus:** {$supportRequest->type}
- **Urgència:** {$supportRequest->urgency}
- **Mòdul:** {$supportRequest->module}
- **URL:** {$supportRequest->url}

📝 **Descripció:** {$supportRequest->description}

✅ **Estat:** Notificacions enviades al remitent i al departament responsable.

Aquesta sol·licitud està sent gestionada pel departament corresponent. Podeu fer seguiment a través del panell d'administració.";
    }
}
