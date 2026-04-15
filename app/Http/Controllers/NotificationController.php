<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Services\FCMService;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendPushNotification;
use App\Jobs\ProcessNotification;
use Illuminate\Support\Facades\Log;
use App\Mail\NotificationMail;

class NotificationController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware de autenticación
        $this->middleware('auth');
        // $this->middleware('auth:api');

        // Verificar permisos específicos para cada acción
        $this->middleware('permission:notifications.create')->only(['create', 'store']);
        $this->middleware('permission:notifications.edit')->only(['edit', 'update']);
        $this->middleware('permission:notifications.delete')->only('destroy');
        $this->middleware('permission:notifications.publish')->only('publish');

    }

    public function index()
    {
        $user = auth()->user();
        
        // Determinar si es vista de administrador vs usuario final
        $isAdminView = $user->isBackoffice();

        // Usuarios con permiso pueden ver todas, los demás solo las propias o asignadas
        if ($isAdminView) {
            $notifications = Notification::latest()->paginate(10);
        } else {
            // Otros ven solo las suyas (relación muchos a muchos)
            $notifications = $user->notifications()
                ->where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->latest()->paginate(10);
        }

        return view('notifications.index', compact('notifications', 'isAdminView'));
    }

    public function create()
    {
        $recipientTypes = [
            'all' => __('site.All_users'),
            'role' =>  __('site.Users_role'),
            'specific' =>  __('site.Specific_users')
        ];

        // Obtener roles como array [id => nombre]
        $roles = \Spatie\Permission\Models\Role::all()->pluck('name', 'id');

        // Obtener todos los usuarios activos
        // $users = User::whereHas('roles')->get();
        // Obtener usuarios que NO tienen el rol 'invited'
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'invited');
        })->get();

        return view('notifications.create', [
            'recipientTypes' => $recipientTypes,
            'roles' => $roles,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string',
            'recipient_type' => 'required|in:all,role,specific',
            'recipient_role' => 'nullable|required_if:recipient_type,role|exists:roles,name',
            'recipient_ids' => 'nullable|required_if:recipient_type,specific|array',
            'recipient_ids.*' => 'exists:users,id',
        ]);

        $user = Auth::user();
        $canPublish = $user->can('notifications.publish');

        $notification = Notification::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'sender_id' => $user->id,
            'recipient_type' => $validated['recipient_type'],
            'recipient_role' => $validated['recipient_type'] === 'role' ? $validated['recipient_role'] : null,
            'recipient_ids' => $validated['recipient_type'] === 'specific' ? $validated['recipient_ids'] : null,
            'is_published' => $canPublish,
            'published_at' => $canPublish ? now() : null,
        ]);

        if (method_exists($this, 'assignRecipients')) {
            $this->assignRecipients($notification);
        }

        return redirect()->route('notifications.index')->with('success', __('site.Notification_created'));
    }

    public function edit(Notification $notification)
    {
        // $this->authorize('update', $notification);
        $recipientTypes = [
        'all' => __('site.All_users'),
        'role' => __('site.Users_role'),
        'specific' => __('site.Specific_users')
        ];

        $roles = Role::all()->pluck('name', 'name');

        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'invited');
        })->get();

        return view('notifications.edit', compact('notification', 'recipientTypes', 'roles', 'users'));

    }

    public function update(Request $request, Notification $notification)
    {
        $this->authorize('update-notification', $notification);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string',
            'recipient_type' => 'required|in:all,role,specific',
            'recipient_role' => 'nullable|required_if:recipient_type,role|exists:roles,name',
            'recipient_ids' => 'nullable|required_if:recipient_type,specific|array',
            'recipient_ids.*' => 'exists:users,id',
        ]);

        $notification->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'recipient_type' => $validated['recipient_type'],
            'recipient_role' => $validated['recipient_type'] === 'role' ? $validated['recipient_role'] : null,
            'recipient_ids' => $validated['recipient_type'] === 'specific' ? $validated['recipient_ids'] : null,
        ]);

        // Actualizar destinatarios si aplica
        $notification->recipients()->detach();
        $this->assignRecipients($notification);

        return redirect()->route('notifications.index')
            ->with('success', __('site.Notification_updated'));
    }

    public function show(Notification $notification)
    {
        //  abort_if(Auth::user()->hasRole('invited'), 403);

        // Marcar como leída al visualizar para el usuario autenticado
        $user = Auth::user();
        
        // Verificar si el usuario es destinatario de esta notificación
        $isRecipient = $notification->recipients()
            ->where('user_id', $user->id)
            ->exists();
            
        if ($isRecipient) {
            // Actualizar el estado de lectura en la tabla pivot
            $notification->recipients()
                ->where('user_id', $user->id)
                ->update([
                    'read' => true,
                    'read_at' => now(),
                ]);
        }

        return view('notifications.show', compact('notification'));
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('notifications.index')->with('success', 'Notificación eliminada');
    }

    public function getUnreadCount()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['count' => 0]);
            }
            $count = $user->unreadNotifications()->count();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Error en getUnreadCount: ' . $e->getMessage());
            return response()->json(['count' => 0, 'error' => 'Error obtenint notificacions']);
        }
    }

    // Publicar
    public function publish(Notification $notification)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor']) &&
            !Auth::user()->can('notifications.publish')) {
            abort(403);
        }

        $notification->update([
            // 'push_sent' => true,
            'is_published' => true,
            'published_at' => now()
        ]);
        $this->assignRecipients($notification);

        return redirect()->route('notifications.index')
            ->with('success', __('site.Notification_published'));
    }

    // marcar como leida
    public function markAsRead($user = null)
    {
        $user = $user ?: auth()->user();
        $this->read_at = now();
        $this->save();

        // O si usas relación muchos a muchos:
        $user->notifications()->updateExistingPivot($this->id, [
            'read_at' => now()
        ]);
    }

    // marcar todas como leidas
    public function markAllAsRead()
    {
        // Solución 1: Usar el método del trait Notifiable
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    protected function assignRecipients(Notification $notification)
    {
        if ($notification->recipient_type === 'all') {
            $users = User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'invited');
            })->pluck('id');
        } elseif ($notification->recipient_type === 'role') {
            $users = User::role($notification->recipient_role)->pluck('id');
        } elseif ($notification->recipient_type === 'specific') {
            $users = collect($notification->recipient_ids);
        } else {
            $users = collect();
        }

        // Sincronizar con valores iniciales
        $notification->recipients()->syncWithPivotValues($users, [
            'email_sent' => false,
            'web_sent' => false,
            'push_sent' => false,
            'read' => false,
        ]);
    }

    public function sendFCM(Request $request, FCMService $fcmService)
    {
        $request->validate([
            'token' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        Log::info('✅ Token FCM recibido y guardado', [
            'user_id' => auth()->id(),
            'token' => $request->fcm_token,
            'hora' => now()->toDateTimeString(),
        ]);

        $fcmService->sendToToken(
            $request->token,
            $request->title,
            $request->body,
            $request->get('data', []) // opcional
        );

        return response()->json(['success' => true]);
    }

    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string',
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // Puedes usar una relación como tokens() o directamente el campo fcm_token
        $user->fcm_token = $request->token;
        $user->device_type = $request->device_type;
        $user->save();

        return response()->json(['message' => 'Token FCM guardado correctamente']);
    }
/* para limpiar
    public function sendFCMNotification(Request $request, FCMService $fcmService)
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
        ]);

        $notification = Notification::findOrFail($request->notification_id);

        if ($notification->push_sent) {
            return response()->json(['message' => 'La notificación ya fue enviada por push.'], 409);
        }

        // Determinar destinatarios
        $users = match ($notification->recipient_type) {
            'all' => User::all(),
            'role' => User::role($notification->recipient_role)->get(),
            'specific' => User::whereIn('id', json_decode($notification->recipient_ids))->get(),
            default => collect(),
        };

        $successTotal = 0;

        foreach ($users as $user) {
            $result = $fcmService->sendToUser($user, $notification->title, $notification->content);
            if (is_array($result) && $result['sent'] > 0) {
                $successTotal++;
            }
        }

        // Actualiza el estado de envío
        $notification->update([
            'push_sent' => true,
            'published_at' => now(),
        ]);

        return response()->json([
            'message' => '🔔 Notificación push enviada correctamente.',
            'users_notified' => $successTotal,
            'total_users' => $users->count(),
        ]);
    }
*/
    public function sendEmail(Notification $notification)
    {
        // Obtener destinatarios según el tipo
        $recipients = $this->getRecipients($notification);
        $sent = 0;
        $failed = 0;

        foreach ($recipients as $user) {
            try {
                // Verificar que el email sea válido
                if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    $failed++;
                    continue;
                }

                // Usar la clase Mailable
                $mail = new NotificationMail($notification, $user);
                
                Mail::to($user->email)->send($mail);
                
                // Marcar como enviado en la tabla pivot
                $notification->recipients()->updateExistingPivot($user->id, [
                    'email_sent' => true,
                    'email_sent_at' => now(),
                ]);

                $sent++;

            } catch (\Exception $e) {
                $failed++;
                Log::error("Error enviando email a {$user->email}: " . $e->getMessage());
            }
        }

        // Marcar la notificación como enviada si al menos uno se envió
        if ($sent > 0) {
            $notification->email_sent = true;
            $notification->save();
        }

        return redirect()->route('notifications.index')
            ->with('success', __('site.Email_sent_success', [
                'sent' => $sent,
                'failed' => $failed,
                'total' => $recipients->count()
            ]));
    }

    /**
     * Obtener destinatarios según el tipo de notificación
     */
    private function getRecipients($notification)
    {
        switch ($notification->recipient_type) {
            case 'all':
                return User::where('email', '!=', null)->get();
                
            case 'role':
                return User::role($notification->recipient_role)
                    ->where('email', '!=', null)
                    ->get();
                
            case 'specific':
                $userIds = $notification->recipient_ids ?? [];
                return User::whereIn('id', $userIds)
                    ->where('email', '!=', null)
                    ->get();
                
            default:
                return collect();
        }
    }

    public function sendWeb(Notification $notification)
    {
        $users = $notification->recipients()->wherePivot('web_sent', false)->get();

        foreach ($users as $user) {
            ProcessNotification::dispatch($notification, $user, 'web');
        }

        return redirect()->route('notifications.index')
            ->with('success', __('site.Web_sent_success', ['count' => $users->count()]));
    }

    public function sendPush(Notification $notification)
    {
        $users = $notification->recipients()->wherePivot('push_sent', false)->get();

        foreach ($users as $user) {
            //  SendPushNotification::dispatch($notification, $user);
            ProcessNotification::dispatch($notification, $user, 'push');
        }

        return redirect()->route('notifications.index')
           ->with('success', __('site.Push_sent_success', ['count' => $users->count()]));
    }

    public function sendPushOld($id, FCMService $fcmService)
    {
        $notification = Notification::findOrFail($id);

        // Evitar duplicados
        if ($notification->push_sent) {
            return redirect()->back()->with('warning', '📤 Esta notificación ya fue enviada por push.');
        }

        // Buscar destinatarios
        $users = match ($notification->recipient_type) {
            'all' => User::all(),
            'role' => User::role($notification->recipient_role)->get(),
            'specific' => User::whereIn('id', json_decode($notification->recipient_ids ?? '[]'))->get(),
        };

        $enviados = 0;

        DB::beginTransaction();

        foreach ($users as $user) {
            $pivot = $notification->users()->where('user_id', $user->id)->first();

            // Evitar si ya fue enviado
            if ($pivot && $pivot->pivot->push_sent) {
                continue;
            }

            $resultado = $fcmService->sendToUser($user, $notification->title, $notification->content);

            if ($resultado !== false && $resultado['sent'] > 0) {
                $enviados++;
                // Marcar como enviado
                $notification->users()->updateExistingPivot($user->id, ['push_sent' => true]);
            }
        }

        $notification->push_sent = true;
        $notification->save();

        DB::commit();

        return redirect()->back()->with('success', "✅ Notificación push enviada a {$enviados} usuarios.");
    }

    public function sendTypedNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:welcome,event,feedback',
        ]);

        $user = User::findOrFail($request->user_id);
        $locale = $user->locale ?? 'es'; // o 'en', por defecto español
        app()->setLocale($locale);

        // Obtener título y cuerpo desde archivos de traducción
        $title = __('site.' . $request->type . '_title');
        $body = __('site.' . $request->type . '_body');

        // Guardar notificación en base de datos
        $notification = Notification::create([
            'type' => $request->type,
            'title' => $title,
            'body' => $body,
        ]);

        $notification->users()->attach($user->id);

        // Enviar FCM
        FCMService::sendNotification($user, $title, $body);

        return response()->json(['message' => 'Notification sent.']);
    }

}
