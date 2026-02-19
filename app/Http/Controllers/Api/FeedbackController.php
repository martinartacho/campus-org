<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'nullable|email',
            'type' => 'nullable|string|max:50',
            'message' => 'required|string|min:5',
        ]);

        // 🧪 Log de depuración
        Log::info('📥 Feedback recibido:', [
            'auth' => Auth::check(),
            'user_id' => Auth::id(),
            'email_detectado' => Auth::user()?->email,
        ]);

        $feedback = Feedback::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'email' => Auth::check()
                ? Auth::user()?->email
                : ($data['email'] ?? null),
            'type' => $data['type'] ?? 'general',
            'message' => $data['message'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback recibido correctamente',
            'data' => $feedback,
        ]);
    }
}
