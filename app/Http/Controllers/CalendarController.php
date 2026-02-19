<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use App\Models\EventQuestion;
use App\Models\EventAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CalendarController extends Controller
{
    /**
     * Mostrar el calendario público
     */
    public function index()
    {
        $eventTypes = EventType::all();
       //  dd($eventTypes);
        return view('calendar.index', compact('eventTypes'));
    }

    /**
     * Mostrar un evento específico
     */
    public function show(Event $event)
    {
        // Verificar que el evento es visible
        if (!$event->visible || 
            ($event->start_visible && $event->start_visible > now()) ||
            ($event->end_visible && $event->end_visible < now())) {
            abort(404);
        }
      
        return view('calendar.show', compact('event'));
    }

    /**
     * Obtener detalles completos de un evento (para AJAX)
     */
    public function eventDetails(Event $event)
    {
       
        // Verificar que el evento es visible
        if (!$event->visible || 
            ($event->start_visible && $event->start_visible > now()) ||
            ($event->end_visible && $event->end_visible < now())) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }

        // Obtener preguntas del evento
        $questions = EventQuestion::where('event_id', $event->id)->get();
        
        // Obtener respuestas del usuario actual (si está autenticado)
        $userResponses = [];
        if (Auth::check()) {
            $userResponses = EventAnswer::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->get()
                ->keyBy('question_id');
        }
        
        // Contar número de respuestas totales para el evento
        $registeredUsers = EventAnswer::where('event_id', $event->id)
            ->distinct('user_id')
            ->count('user_id');
            
        // Contar número de preguntas respondidas por el usuario actual
        $questionsAnswered = 0;
        if (Auth::check()) {
            $questionsAnswered = EventAnswer::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->count();
        }
        // PARA BORRAR dd('Event: '.  $event,  'questions: '.$questions, 'userResponses: '. $userResponses, 'registeredUsers: '. $registeredUsers, 'questionsAnswered: '. $questionsAnswered);
        return response()->json([
            'id' => $event->id,
            'title' => $event->title,
            'start' => $event->start,
            'end' => $event->end,
            'allDay' => $event->all_day,
            'start_visible' => $event->start_visible,
            'end_visible' => $event->end_visible,
            'max_users' => $event->max_users,
            'extendedProps' => [
                'description' => $event->description,
                'event_type' => $event->eventType->name ?? 'None',
                'has_questions' => $questions->count() > 0,
                'questions_count' => $questions->count(),
                'questions_answered' => $questionsAnswered,
                'registered_users' => $registeredUsers,
                'questions' => $questions->map(function($question) use ($userResponses) {
                    return [
                        'id' => $question->id,
                        'question' => $question->question, // Cambiado de question_text a question
                        'type' => $question->type,
                        'options' => $question->options,
                        'required' => $question->required,
                        'user_response' => isset($userResponses[$question->id]) ? $userResponses[$question->id]->answer : null
                    ];
                }),
                'user_responses' => $userResponses->map(function($response) {
                    return [
                        'question_id' => $response->question_id,
                        'answer' => $response->answer
                    ];
                })->values()
            ]
        ]);
    }


    /**
     * Guardar respuestas a las preguntas de un evento
     */
    public function saveAnswers(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'responses' => 'required|array'
        ]);

        $event = Event::findOrFail($request->event_id);
        
        // Verificar que el evento es visible y acepta respuestas
        if (!$event->visible || 
            ($event->start_visible && $event->start_visible > now()) ||
            ($event->end_visible && $event->end_visible < now())) {
            return response()->json([
                'success' => false,
                'message' => 'Este evento no acepta respuestas en este momento'
            ], 403);
        }

        // Verificar límite de usuarios
        if ($event->max_users) {
            $currentUsers = EventAnswer::where('event_id', $event->id)
                ->distinct('user_id')
                ->count('user_id');
                
            if ($currentUsers >= $event->max_users) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se ha alcanzado el límite máximo de participantes para este evento'
                ], 403);
            }
        }

        try {
            // Guardar cada respuesta
            foreach ($request->responses as $questionId => $answer) {
                // Validar que la pregunta existe y pertenece al evento
                $question = EventQuestion::where('id', $questionId)
                    ->where('event_id', $event->id)
                    ->first();
                    
                if (!$question) {
                    continue; // Saltar preguntas que no existen o no pertenecen al evento
                }
                
                // Preparar la respuesta para guardar
                $answerToSave = '';
                
                if (is_array($answer)) {
                    // Para respuestas múltiples (checkboxes)
                    $filteredAnswers = array_filter($answer, function($value) {
                        return !empty($value) && trim($value) !== '';
                    });
                    
                    if (!empty($filteredAnswers)) {
                        $answerToSave = implode(',', $filteredAnswers);
                    }
                } else {
                    // Para respuestas simples (texto o radio)
                    $answerToSave = trim($answer);
                }
                
                // Solo guardar si la respuesta no está vacía
                if (!empty($answerToSave)) {
                    EventAnswer::updateOrCreate(
                        [
                            'event_id' => $event->id,
                            'question_id' => $questionId,
                            'user_id' => Auth::id()
                        ],
                        [
                            'answer' => $answerToSave
                        ]
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Respuestas guardadas correctamente'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al guardar respuestas: ' . $e->getMessage());
            \Log::error('Datos de la solicitud: ', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al guardar las respuestas'
            ], 500);
        }
    }


    /**
     * Obtener eventos para el calendario (JSON)
     */
    public function events(Request $request)
    {
        $query = Event::where('visible', true)
            ->where(function($query) {
                $query->whereNull('start_visible')
                    ->orWhere('start_visible', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_visible')
                    ->orWhere('end_visible', '>=', now());
            });

        // Filtrar por tipo de evento si se especifica
        if ($request->has('event_type_id')) {
            $query->where('event_type_id', $request->event_type_id);
        }

        // Filtrar por rango de fechas si se especifica
        if ($request->has('start') && $request->has('end')) {
            $query->where(function($q) use ($request) {
                $q->whereBetween('start', [$request->start, $request->end])
                  ->orWhereBetween('end', [$request->start, $request->end])
                  ->orWhere(function($q) use ($request) {
                      $q->where('start', '<=', $request->start)
                        ->where('end', '>=', $request->end);
                  });
            });
        }

        $events = $query->get();

        return response()->json($events->map(function ($event) {
            // Verificar si el evento tiene preguntas
            $hasQuestions = EventQuestion::where('event_id', $event->id)->exists();
            
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start,
                'end' => $event->end,
                'color' => $event->color ?? ($event->eventType->color ?? '#3c8dbc'),
                'url' => route('calendar.event.show', $event->id),
                'extendedProps' => [
                    'description' => $event->description,
                    'event_type' => $event->eventType->name ?? 'None',
                    'max_users' => $event->max_users,
                    'has_questions' => $hasQuestions,
                ]
            ];
        }));
    }

    /**
     * Mostrar eventos próximos (para el dashboard)
     */
    public function upcomingEvents($limit = 5)
    {
        $events = Event::where('visible', true)
            ->where(function($query) {
                $query->whereNull('start_visible')
                    ->orWhere('start_visible', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_visible')
                    ->orWhere('end_visible', '>=', now());
            })
            ->where('start', '>=', now())
            ->orderBy('start', 'asc')
            ->limit($limit)
            ->get();

        return $events;
    }

}