<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\CampusCourse;
use App\Models\User;
use App\Models\CampusStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TeacherNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar formulario de creación de notificación
     */
    public function create($courseId)
    {
        $course = CampusCourse::findOrFail($courseId);
        $this->authorizeCourse($course);
        
        // Obtener estudiantes del curso
        $students = $course->students()
            ->where(function($query) {
                $query->where('campus_course_student.academic_status', 'active')
                      ->orWhere('campus_course_student.academic_status', 'enrolled');
            })
            ->with('user')
            ->get();
        
        return view('campus.teacher.notifications.create', compact('course', 'students'));
    }

    /**
     * Guardar nueva notificación
     */
    public function store(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'type' => 'required|in:general,reminder,announcement,assignment,grade,emergency',
            'recipient_type' => 'required|in:all,specific',
            'recipient_ids' => 'required_if:recipient_type,specific|array',
            'recipient_ids.*' => 'exists:users,id',
            'send_immediately' => 'boolean',
        ]);

        $course = CampusCourse::findOrFail($courseId);
        $this->authorizeCourse($course);

        try {
            DB::beginTransaction();

            // Crear notificación usando el sistema unificado
            $notification = Notification::create([
                'title' => $request->title,
                'content' => $request->content,
                'type' => 'teacher', // Tipo específico para teacher notifications
                'sender_id' => Auth::id(),
                'recipient_type' => $request->recipient_type,
                'recipient_ids' => $request->recipient_ids ?? [],
                'is_published' => $request->boolean('send_immediately'),
                'published_at' => $request->boolean('send_immediately') ? now() : null,
            ]);

            // Asignar destinatarios y enviar canales
            if ($request->boolean('send_immediately')) {
                $this->assignNotificationRecipients($notification, $request->recipient_type, $request->recipient_ids ?? [], $course);
                $this->sendNotificationChannels($notification);
            }

            DB::commit();

            $message = $request->boolean('send_immediately') 
                ? 'Notificación enviada correctament' 
                : 'Notificación guardada com a esborrany';

            return redirect()->route('campus.teacher.courses.students', $course->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating teacher notification: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error al crear la notificación: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar lista de notificaciones del curso
     */
    public function index($courseId)
    {
        $course = CampusCourse::findOrFail($courseId);
        $this->authorizeCourse($course);

        $notifications = Notification::where('type', 'teacher')
            ->where('sender_id', Auth::id())
            ->with(['sender', 'recipients'])
            ->latest('published_at')
            ->paginate(15);

        return view('campus.teacher.notifications.index', compact('course', 'notifications'));
    }

    /**
     * Mostrar detalles de una notificación
     */
    public function show($courseId, $notificationId)
    {
        $course = CampusCourse::findOrFail($courseId);
        $this->authorizeCourse($course);

        $notification = Notification::with(['sender', 'recipients'])
            ->findOrFail($notificationId);

        // Verificar que el teacher sea el autor
        if ($notification->sender_id !== Auth::id()) {
            abort(403, 'No tienes acceso a esta notificación');
        }

        return view('campus.teacher.notifications.show', compact('course', 'notification'));
    }

    /**
     * Marcar notificación como leída
     */
    public function markAsRead($notificationId)
    {
        try {
            $notification = Notification::findOrFail($notificationId);
            
            // Verificar que el usuario sea destinatario
            $isRecipient = $notification->recipients()
                ->where('user_id', Auth::id())
                ->exists();
            
            if (!$isRecipient) {
                return response()->json(['error' => 'No eres destinatario de esta notificación'], 403);
            }

            // Marcar como leído
            $notification->recipients()
                ->where('user_id', Auth::id())
                ->update([
                    'read' => true,
                    'read_at' => now(),
                ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json(['error' => 'Error al marcar como leído'], 500);
        }
    }

    /**
     * Asignar destinatarios a la notificación
     */
    private function assignNotificationRecipients($notification, $recipientType, $specificIds = [], $course)
    {
        try {
            $recipientIds = [];

            if ($recipientType === 'all') {
                // Obtener todos los estudiantes del curso
                $students = $course->students()
                    ->where(function($query) {
                        $query->where('campus_course_student.academic_status', 'active')
                              ->orWhere('campus_course_student.academic_status', 'enrolled');
                    })
                    ->get();
                
                $recipientIds = $students->pluck('user_id')->filter()->toArray();
            } else {
                // Usar IDs específicos
                $recipientIds = $specificIds;
            }

            // Asignar destinatarios
            $notification->recipients()->attach($recipientIds);

            Log::info('Teacher notification recipients assigned', [
                'notification_id' => $notification->id,
                'recipient_type' => $recipientType,
                'recipients_count' => count($recipientIds)
            ]);

        } catch (\Exception $e) {
            Log::error('Error assigning notification recipients: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enviar notificación a través de los canales
     */
    private function sendNotificationChannels($notification)
    {
        try {
            // Marcar como enviado en web
            $notification->update(['web_sent' => true]);

            // Aquí se podrían agregar más canales (email, push, etc.)
            // Por ahora solo marcamos como enviado en web

            Log::info('Teacher notification sent via channels', [
                'notification_id' => $notification->id,
                'channels' => ['web']
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending notification channels: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Autorizar que el profesor tenga acceso al curso
     */
    private function authorizeCourse(CampusCourse $course)
    {
        $teacher = Auth::user()->teacher;
        
        // Verificar que el usuario tenga perfil de profesor
        abort_if(!$teacher, 403, 'No tienes un perfil de profesor');
        
        // Verificar que el profesor esté asignado a este curso
        $isAssigned = $course->teachers()
            ->where('campus_teachers.id', $teacher->id)
            ->exists();
            
        abort_if(!$isAssigned, 403, 'No estás asignado a este curso');
    }
}
