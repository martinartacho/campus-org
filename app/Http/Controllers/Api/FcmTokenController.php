<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Services\FCMService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FcmTokenController extends Controller
{
    public function getNotificationsApi(Request $request)
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = $user->notifications()
            ->take(50)
            ->get()
            ->map(function ($notification) {
                return [
                'id' => $notification->id,
                'title' => $notification->title,
                'body' => $notification->content,
                'read_at' => $notification->pivot->read_at,
                'created_at' => $notification->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function saveFcmToken(Request $request)
    {

        Log::info('✅ Dentro de saveFcmToken');
        Log::info('🧪 Token recibido en request', ['token' => $request->token]);

        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string',
            'device_name' => 'nullable|string',
        ]);

        $user = auth()->user();

        FcmToken::updateOrCreate(
            ['user_id' => $user->id, 'token' => $request->token],
            [
                'device_type' => $request->device_type,
                'device_name' => $request->device_name,
                'last_used_at' => now(),
                'is_valid' => true,
            ]
        );

        Log::info('✅ Token FCM recibido y guardado', [
        'user_id' => auth()->id(),
        'token' => $request->token,
        'hora' => now()->toDateTimeString(),
        ]);

        return response()->json(['status' => 'success']);
    }

    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
            'device_type' => 'required|string|in:web,mobile'
        ]);

        if ($validator->fails()) {
            Log::warning('en store error 422');
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Obtener usuario autenticado
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Guardar token (ajusta según tu modelo)
            $user->fcm_tokens()->updateOrCreate(
                ['device_type' => $request->device_type],
                ['token' => $request->fcm_token]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Token guardado correctamente'
            ]);

        } catch (\Exception $e) {
            Log::warning('En store errro del servidor');

            return response()->json([
                'status' => 'error',
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function unreadCount(Request $request)
    {
        $user = auth()->user();

        $count = $user->notifications()
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    // marcar como leida
    public function markAsReadApi($notificationId) // Asegúrate de recibir el parámetro
    {
        Log::warning('Dentro de markAsReadApi con ID: '.$notificationId);

        try {
            $user = Auth::user();

            // Opción 1: Para relación muchos-a-muchos (pivote)
            $affected = $user->notifications()
                ->where('notification_id', $notificationId)
                ->update([
                    'read_at' => now(),
                    'read' => true
                 ]);
            Log::warning("Se actualizó notificación para el usuario: ". $user->id . " notificationId: " .$notificationId);
            // Opción 2: Si es una relación directa
            // $notification = Notification::findOrFail($notificationId);
            // $notification->read_at = now();
            // $notification->save();

            if ($affected === 0) {
                Log::warning("affecte : No se actualizó ninguna notificación para el usuario: ".$user->id." notificationId ".$notificationId);
                return response()->json(['success' => false, 'message' => 'Notificación no encontrada'], 404);
            } else {
                // Log::warnig("affected es else  ");
            }

            Log::warning("Notificación $notificationId marcada como leída para el usuario: ".$user->id. " ". $notificationId);
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error("Error en markAsReadApi: ".$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno'], 500);
        }
    }

}
