<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use App\Models\Notification;
use App\Models\User;
use App\Models\TaskBoard;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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

    // === MÈTODES DE TASQUES (TEMPORAL) ===
    
    /**
     * Display the boards listing page.
     */
    public function taskBoardsIndex()
    {
        $boards = \App\Models\TaskBoard::with(['lists', 'creator', 'tasks'])
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        // Afegir estadístiques a cada tauler
        $boards->each(function ($board) {
            $board->stats = [
                'total_tasks' => $board->tasks()->count(),
                'completed_tasks' => $board->tasks()->where('status', 'completed')->count(),
                'in_progress_tasks' => $board->tasks()->where('status', 'in_progress')->count(),
                'overdue_tasks' => $board->tasks()
                    ->where('due_date', '<', now())
                    ->where('status', '!=', 'completed')
                    ->count(),
            ];
        });
            
        return view('tasks.index', compact('boards'));
    }

    /**
     * Display the specified board.
     */
    public function taskBoardShow($boardId)
    {
        $board = TaskBoard::with(['lists', 'tasks.assignedUser', 'creator'])->findOrFail($boardId);
        $this->authorize('view', $board);
        
        // Carregar usuaris agrupats per rol
        $usersByRole = \App\Models\User::select('id', 'name', 'email')
            ->with('roles')
            ->orderBy('name')
            ->get()
            ->groupBy(function($user) {
                if ($user->roles->isEmpty()) {
                    return 'Sense rol';
                }
                return $user->roles->first()->name;
            });
        
        return view('tasks.board', compact('board', 'usersByRole'));
    }

    /**
     * Show the form for creating a new board.
     */
    public function taskBoardCreate()
    {
        // Carregar usuaris agrupats per rol
        $usersByRole = \App\Models\User::select('id', 'name', 'email')
            ->with('roles')
            ->orderBy('name')
            ->get()
            ->groupBy(function($user) {
                if ($user->roles->isEmpty()) {
                    return 'Sense rol';
                }
                return $user->roles->first()->name;
            });
        
        return view('tasks.create-board', compact('usersByRole'));
    }

    /**
     * Store a newly created board.
     */
    public function taskBoardStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:team,course,department,global',
            'entity_id' => 'nullable|integer',
            'visibility' => 'required|in:private,team,public',
        ]);

        $board = TaskBoard::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'entity_id' => $validated['entity_id'] ?? null,
            'created_by' => Auth::id(),
            'visibility' => $validated['visibility'],
        ]);

        // Create default lists
        $board->createDefaultLists();

        return redirect()->route('tasks.boards.show', $board)
            ->with('success', 'Tauler creat correctament');
    }

    /**
     * Show the form for editing the specified board.
     */
    public function taskBoardEdit($boardId)
    {
        $board = TaskBoard::findOrFail($boardId);
        $this->authorize('update', $board);
        return view('tasks.edit-board', compact('board'));
    }

    /**
     * Update the specified board.
     */
    public function taskBoardUpdate(Request $request, $boardId)
    {
        $board = TaskBoard::findOrFail($boardId);
        $this->authorize('update', $board);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:private,team,public',
        ]);

        $board->update($validated);

        return redirect()->route('tasks.boards.show', $board)
            ->with('success', 'Tauler actualitzat correctament');
    }

    /**
     * Remove the specified board.
     */
    public function taskBoardDestroy($boardId)
    {
        $board = TaskBoard::findOrFail($boardId);
        $this->authorize('delete', $board);
        
        $board->delete();

        return redirect()->route('tasks.boards.index')
            ->with('success', 'Tauler eliminat correctament');
    }

    // === MÈTODES API DE TASQUES ===
    
    /**
     * Create a new task (API).
     */
    public function apiCreateTask(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'list_id' => 'required|exists:task_lists,id',
                'priority' => 'required|in:low,medium,high,urgent',
                'start_date' => 'nullable|date',
                'due_date' => 'nullable|date|after_or_equal:start_date',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            $task = \App\Models\Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'list_id' => $validated['list_id'],
                'priority' => $validated['priority'],
                'start_date' => $validated['start_date'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'assigned_to' => $validated['assigned_to'] ?? null,
                'status' => 'pending',
                'order_in_list' => \App\Models\Task::where('list_id', $validated['list_id'])->max('order_in_list') + 1,
                'created_by' => Auth::id(),
            ]);

            // Carregar relacions per la resposta
            $task->load(['assignedUser', 'creator', 'list']);

            return response()->json($task, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error creant la tasca',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move a task to another list (API).
     */
    public function apiMoveTask(Request $request, $taskId)
    {
        try {
            $task = \App\Models\Task::findOrFail($taskId);
            
            // Autorització bàsica - TODO: millorar amb policies
            if ($task->created_by !== Auth::id() && $task->assigned_to !== Auth::id()) {
                return response()->json(['error' => 'No autoritzat'], 403);
            }

            // Validar les dades d'entrada
            $validated = $request->validate([
                'list_id' => 'required|exists:task_lists,id',
                'order' => 'nullable|integer|min:0'
            ]);

            // Actualitzar la tasca
            $task->list_id = $validated['list_id'];
            $task->order_in_list = $validated['order'] ?? 0;
            $task->save();

            // Carregar relacions per la resposta
            $task->load(['assignedUser', 'creator', 'list']);

            return response()->json([
                'success' => true,
                'task' => $task
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Tasca no trobada'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validació',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error movent la tasca'], 500);
        }
    }

    // === MÈTODES API D'USUARIS ===
    
    /**
     * Get users grouped by role.
     */
    public function apiUsersByRole()
    {
        $users = \App\Models\User::select('id', 'name', 'email')
            ->with('roles')
            ->orderBy('name')
            ->get()
            ->groupBy(function($user) {
                if ($user->roles->isEmpty()) {
                    return 'Sense rol';
                }
                return $user->roles->first()->name;
            });
            
        return response()->json($users);
    }
    
    /**
     * Get users by specific role.
     */
    public function apiUsersByRoleName($role)
    {
        $users = \App\Models\User::select('id', 'name', 'email')
            ->role($role)
            ->orderBy('name')
            ->get();
            
        return response()->json($users);
    }
}
