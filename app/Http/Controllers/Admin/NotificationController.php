<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
        $this->middleware('permission:notifications.send');
    }

    /**
     * Show notification creation form
     */
    public function create(): View
    {
        return view('admin.notifications.create');
    }

    /**
     * Store new notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:specific,role,roles,filtered',
            'recipient_ids' => 'required_if:recipient_type,specific|array',
            'recipient_role' => 'required_if:recipient_type,role|string',
            'recipient_roles' => 'required_if:recipient_type,roles|array',
            'filters' => 'nullable|array'
        ]);

        // Generate support ticket if needed
        $ticketId = null;
        $isSupportTicket = false;
        $templateType = null;
        $processedContent = $request->content;

        if ($request->type === 'support') {
            $ticketId = $this->generateUniqueTicketId();
            $isSupportTicket = true;
            $templateType = 'support';
            $processedContent = $this->generateSupportTicketContent($request->content, $ticketId);
        } else {
            $templateType = $request->type;
            $processedContent = $this->applyTemplate($request->type, $request->content);
        }

        $options = [
            'type' => $request->type ?? 'general',
            'ticket_id' => $ticketId,
            'template_type' => $templateType,
            'is_support_ticket' => $isSupportTicket,
        ];

        switch ($request->recipient_type) {
            case 'specific':
                $this->notificationService->sendToUsers(
                    $request->recipient_ids,
                    $request->title,
                    $processedContent,
                    $options
                );
                break;

            case 'role':
                $this->notificationService->sendToRole(
                    $request->recipient_role,
                    $request->title,
                    $processedContent,
                    $options
                );
                break;

            case 'roles':
                $this->notificationService->sendToRoles(
                    $request->recipient_roles,
                    $request->title,
                    $processedContent,
                    $options
                );
                break;

            case 'filtered':
                $this->notificationService->sendToFiltered(
                    $request->filters ?? [],
                    $request->title,
                    $processedContent,
                    $options
                );
                break;
        }

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notificació enviada correctament.');
    }

    /**
     * Show notification dashboard
     */
    public function index(): View
    {
        $stats = $this->notificationService->getNotificationStats();
        
        return view('admin.notifications.index', compact('stats'));
    }

    /**
     * Get users for AJAX requests
     */
    public function getUsers(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->take(20)->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    /**
     * Generate unique ticket ID
     */
    public function generateUniqueTicketId(): string
    {
        do {
            $date = now()->format('Ymd');
            $sequence = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $ticketId = "ADM-{$date}-{$sequence}";
        } while (\App\Models\Notification::where('ticket_id', $ticketId)->exists());

        return $ticketId;
    }

    /**
     * Generate support ticket content
     */
    private function generateSupportTicketContent(string $userContent, string $ticketId): string
    {
        $user = auth()->user();
        $date = now()->format('d/m/Y H:i');
        
        return "⚠️ **NOVA SOL·LICITUD DE SUPORT ASSIGNADA**

📋 **Detalls del Ticket:**
- **Número de Ticket:** {$ticketId}
- **Remitent:** {$user->name} ({$user->email})
- **Departament:** admin
- **Tipus:** consultation
- **Urgència:** medium
- **Data:** {$date}

📝 **Descripció:** 
{$userContent}

🎯 **Acció Requerida:**
Aquesta sol·licitud ha estat assignada al vostre departament. Si us plau, reviseu-la i proporcioneu una resposta al més aviat possible.

Podeu gestionar aquesta sol·licitud a través del sistema de notificaciones o contactar directament amb el remitent.
Destinataris: Usuaris amb rol: admin
Estat: Publicat el {$date}";
    }

    /**
     * Apply template based on type
     */
    private function applyTemplate(string $type, string $content): string
    {
        switch ($type) {
            case 'academic':
                return $this->generateAcademicTemplate($content);
            case 'administrative':
                return $this->generateAdministrativeTemplate($content);
            default:
                return $content;
        }
    }

    /**
     * Generate academic template
     */
    private function generateAcademicTemplate(string $content): string
    {
        $date = now()->format('d/m/Y');
        return "📚 **COMUNICACIÓ ACADÈMICA**

📅 **Data:** {$date}

📝 **Contingut:**
{$content}

---
*Departament d'Acadèmia - Campus UPG*";
    }

    /**
     * Generate administrative template
     */
    private function generateAdministrativeTemplate(string $content): string
    {
        $date = now()->format('d/m/Y');
        return "⚙️ **COMUNICACIÓ ADMINISTRATIVA**

📅 **Data:** {$date}

📝 **Contingut:**
{$content}

---
*Departament d'Administració - Campus UPG*";
    }
}
