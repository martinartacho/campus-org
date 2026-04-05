<?php

namespace App\Http\Controllers;

use App\Models\CampusTeacher;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TeacherNotificationController extends Controller
{
    /**
     * Mostrar el llistat de notificacions enviades al professorat
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Verificar permisos
        if (!$user->hasRole(['admin', 'manager', 'treasury'])) {
            abort(403, 'No tens permisos per veure les notificacions del professorat');
        }

        $query = Notification::with(['sender', 'recipients'])
            ->whereHas('recipients', function($q) {
                $q->whereHas('teacherProfile');
            });

        // Filtres
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        if ($request->filled('sender_id')) {
            $query->where('sender_id', $request->input('sender_id'));
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Remitents possibles per filtre
        $senders = User::whereIn('id', function($q) {
            $q->select('sender_id')
              ->from('notifications')
              ->whereHas('recipients', function($subQ) {
                  $subQ->whereHas('teacherProfile');
              })
              ->distinct();
        })->orderBy('name')->get();

        return view('campus.teachers.notifications.index', compact(
            'notifications',
            'senders'
        ));
    }

    /**
     * Mostrar el formulari de creació de notificacions
     */
    public function create(): View
    {
        $user = Auth::user();
        
        // Verificar permisos
        if (!$user->hasRole(['admin', 'manager', 'treasury'])) {
            abort(403, 'No tens permisos per crear notificacions per al professorat');
        }

        // Obtenir tots els professors amb filtres
        $teachers = CampusTeacher::with('user')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Estadístiques per als filtres
        $stats = [
            'total' => $teachers->count(),
            'with_iban' => $teachers->whereNotNull('iban')->count(),
            'without_iban' => $teachers->whereNull('iban')->count(),
            'with_pdfs' => $teachers->filter(fn($t) => $t->hasPdfs())->count(),
            'without_pdfs' => $teachers->filter(fn($t) => !$t->hasPdfs())->count(),
            'by_payment_type' => $teachers->groupBy('payment_type')->map->count(),
        ];

        return view('campus.teachers.notifications.create', compact(
            'teachers',
            'stats'
        ));
    }

    /**
     * Guardar una nova notificació
     */
    public function store(Request $request): Response
    {
        $user = Auth::user();
        
        // Verificar permisos
        if (!$user->hasRole(['admin', 'manager', 'treasury'])) {
            abort(403, 'No tens permisos per crear notificacions per al professorat');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'type' => 'required|in:info,warning,success,error',
            'recipient_type' => 'required|in:all,filtered,specific',
            'send_immediately' => 'boolean',
            'filters' => 'array',
            'recipient_ids' => 'array',
            'recipient_ids.*' => 'exists:users,id',
        ]);

        try {
            // Determinar destinataris
            $recipientIds = $this->getRecipientIds($validated);

            if (empty($recipientIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No s\'han trobat destinataris per als filtres seleccionats'
                ], 422);
            }

            // Crear notificació
            $notification = Notification::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'type' => $validated['type'],
                'sender_id' => $user->id,
                'is_published' => $validated['send_immediately'] ?? false,
                'published_at' => ($validated['send_immediately'] ?? false) ? now() : null,
            ]);

            // Associar destinataris
            $notification->recipients()->attach($recipientIds);

            // Enviar immediatament si s'ha sol·licitat
            if ($validated['send_immediately'] ?? false) {
                $this->sendNotification($notification);
            }

            Log::info('Notificació de professorat creada', [
                'notification_id' => $notification->id,
                'sender_id' => $user->id,
                'recipients_count' => count($recipientIds),
                'recipient_type' => $validated['recipient_type']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notificació creada correctament',
                'notification' => $notification,
                'recipients_count' => count($recipientIds)
            ]);

        } catch (\Exception $e) {
            Log::error('Error creant notificació de professorat', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creant la notificació: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar els detalls d'una notificació
     */
    public function show(Notification $notification): View
    {
        $user = Auth::user();
        
        // Verificar permisos
        if (!$user->hasRole(['admin', 'manager', 'treasury'])) {
            abort(403, 'No tens permisos per veure aquesta notificació');
        }

        $notification->load(['sender', 'recipients.teacherProfile']);

        return view('campus.teachers.notifications.show', compact('notification'));
    }

    /**
     * Eliminar una notificació
     */
    public function destroy(Notification $notification): Response
    {
        $user = Auth::user();
        
        // Verificar permisos
        if (!$user->hasRole(['admin', 'manager'])) {
            abort(403, 'No tens permisos per eliminar notificacions');
        }

        try {
            $notification->recipients()->detach();
            $notification->delete();

            Log::info('Notificació de professorat eliminada', [
                'notification_id' => $notification->id,
                'deleted_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notificació eliminada correctament'
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminant notificació de professorat', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error eliminant la notificació'
            ], 500);
        }
    }

    /**
     * Determinar els IDs dels destinataris segons els filtres
     */
    private function getRecipientIds(array $validated): array
    {
        $query = CampusTeacher::with('user');

        switch ($validated['recipient_type']) {
            case 'all':
                return $query->pluck('user_id')->filter()->toArray();

            case 'filtered':
                $filters = $validated['filters'] ?? [];
                
                // Filtrar per IBAN
                if (isset($filters['iban'])) {
                    if ($filters['iban'] === 'with') {
                        $query->whereNotNull('iban');
                    } elseif ($filters['iban'] === 'without') {
                        $query->whereNull('iban');
                    }
                }

                // Filtrar per PDFs
                if (isset($filters['pdfs'])) {
                    if ($filters['pdfs'] === 'with') {
                        $query->whereHas('consents');
                    } elseif ($filters['pdfs'] === 'without') {
                        $query->whereDoesntHave('consents');
                    }
                }

                // Filtrar per tipus de pagament
                if (!empty($filters['payment_types'])) {
                    $query->whereIn('payment_type', $filters['payment_types']);
                }

                // Filtrar per cursos
                if (!empty($filters['course_ids'])) {
                    $query->whereHas('courses', function($q) use ($filters) {
                        $q->whereIn('courses.id', $filters['course_ids']);
                    });
                }

                return $query->pluck('user_id')->filter()->toArray();

            case 'specific':
                return $validated['recipient_ids'] ?? [];

            default:
                return [];
        }
    }

    /**
     * Enviar una notificació
     */
    private function sendNotification(Notification $notification): void
    {
        // Aquí implementaríem l'enviament real
        // Email, web notifications, push notifications, etc.
        
        Log::info('Notificació de professorat enviada', [
            'notification_id' => $notification->id,
            'recipients_count' => $notification->recipients->count()
        ]);
    }
}
