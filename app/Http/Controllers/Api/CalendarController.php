<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventAnswer;
use App\Models\EventQuestion;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Listar próximos 5 eventos visibles
     */
    public function index()
    {
        $now = now();

        $events = Event::where('visible', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_visible')->orWhere('start_visible', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_visible')->orWhere('end_visible', '>=', $now);
            })
            ->orderBy('start', 'asc')
            ->take(5)
            ->withCount('questions')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start,
                    'end' => $event->end,
                    'color' => $event->color,
                    'has_questions' => $event->questions_count > 0,
                    'visible' => $event->visible, 
                    'start_visible' => $event->start_visible, 
                    'end_visible' => $event->end_visible, 
                ];
            })
        ]);
    }

    /**
     * Mostrar detalle de evento
     */
    public function show($id)
    {
        $event = Event::with('questions')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start' => $event->start,
                'end' => $event->end,
                'color' => $event->color,
                'max_users' => $event->max_users,
                'questions' => $event->questions,
            ]
        ]);
    }

    /**
     * Guardar respuesta a una pregunta
     */
    public function storeAnswer(Request $request, $eventId)
    {
        $data = $request->validate([
            'question_id' => 'required|exists:event_questions,id',
            'answer' => 'required|string',
        ]);

        $user = Auth::user();

        // Verificar límite de usuarios
        $event = Event::findOrFail($eventId);
        if ($event->max_users !== null) {
            $totalAnswers = EventAnswer::where('event_id', $event->id)
                ->where('question_id', $data['question_id'])
                ->count();

            if ($totalAnswers >= $event->max_users) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Límite de usuarios alcanzado'
                ], 403);
            }
        }

        $answer = EventAnswer::updateOrCreate(
            [
                'event_id' => $eventId,
                'user_id' => $user->id,
                'question_id' => $data['question_id']
            ],
            ['answer' => $data['answer']]
        );

        return response()->json([
            'status' => 'success',
            'data' => $answer
        ]);
    }

    /**
     * Editar respuesta
     */
    public function updateAnswer(Request $request, $id)
    {
        $data = $request->validate([
            'answer' => 'required|string',
        ]);

        $answer = EventAnswer::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $answer->update(['answer' => $data['answer']]);

        return response()->json([
            'status' => 'success',
            'data' => $answer
        ]);
    }

    /**
     * Eliminar respuesta
     */
    public function destroyAnswer($id)
    {
        $answer = EventAnswer::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $answer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Respuesta eliminada'
        ]);
    }

    /**
     * Obtener respuestas del usuario actual para un evento
     */
    public function getUserResponses($eventId)
    {
        $user = Auth::user();
        
        $responses = EventAnswer::where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->get()
            ->mapWithKeys(function ($answer) {
                return [$answer->question_id => $answer->answer];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'answers' => $responses
            ]
        ]);
    }
}
