<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAnswer; 
use Illuminate\Http\Request;
use App\Services\ExportService;
use App\Exports\EventAnswersExport;

class EventAnswerController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }
     
     /**
     * Display a listing of the answers for an event.
     */
    public function index(Event $event)
    {
        $this->authorize('viewAny', EventAnswer::class);
        
        // Obtener todas las preguntas del evento
        $questions = $event->questions()->orderBy('id')->get();
        
        // Obtener todas las respuestas relacionadas con las preguntas de este evento
        $answers = EventAnswer::whereHas('question', function($query) use ($event) {
            $query->where('event_id', $event->id);
        })->with(['user', 'question'])->get();
        
        // Agrupar respuestas por usuario
        $groupedAnswers = [];
        foreach ($answers as $answer) {
            $userId = $answer->user_id;
            
            if (!isset($groupedAnswers[$userId])) {
                $groupedAnswers[$userId] = [
                    'user' => $answer->user,
                    'answers' => collect()
                ];
            }
            
            $groupedAnswers[$userId]['answers']->push($answer);
        }
        return view('admin.events.answers.index', compact('event', 'questions', 'groupedAnswers', 'answers'));
    }

    /**
     * Show the form for creating a new answer.
     */
    public function create(Event $event)
    {
        $this->authorize('create', EventAnswer::class);
        
        $questions = $event->questions;
        return view('admin.events.answers.create', compact('event', 'questions'));
    }

    /**
     * Store a newly created answer.
     */
    public function store(Request $request, Event $event)
    {
        $this->authorize('create', EventAnswer::class);
        
        $validated = $request->validate([
            'question_id' => 'required|exists:event_questions,id',
            'answer' => 'required|string|max:1000',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['event_id'] = $event->id;

        EventAnswer::create($validated);

        return redirect()->route('admin.events.answers.index', $event)
            ->with('success', __('site.Answer created successfully.'));
    }

    /**
     * Display the specified answer.
     */
    public function show(Event $event, EventAnswer $answer)
    {
        $this->authorize('view', $answer);
        
        return view('admin.events.answers.show', compact('event', 'answer'));
    }

    /**
     * Remove the specified answer.
     */
    public function destroy(Event $event, EventAnswer $answer)
    {
        $this->authorize('delete', $answer);
        
        $answer->delete();

        return redirect()->route('admin.events.answers.index', $event)
            ->with('success', __('site.Answer deleted successfully.'));
    }

    public function print(Event $event)
    {
        $this->authorize('viewAny', EventAnswer::class);
        // Obtener datos (misma lógica que el método index)
        $questions = $event->questions()->orderBy('id')->get();
        $answers = $event->answers()->with(['user', 'question'])->get();
        
        $groupedAnswers = [];
        foreach ($answers as $answer) {
            $userId = $answer->user_id;
            
            if (!isset($groupedAnswers[$userId])) {
                $groupedAnswers[$userId] = [
                    'user' => $answer->user,
                    'answers' => collect()
                ];
            }
            
            $groupedAnswers[$userId]['answers']->push($answer);
        }
        
        return view('admin.events.answers.print', 
            compact('event', 'questions', 'groupedAnswers'));
    }


    public function export(Event $event, $format)
    {
        $this->authorize('viewAny', EventAnswer::class);
        
        // Obtener datos (misma lógica que el método index)
        $questions = $event->questions()->orderBy('id')->get();
        $answers = $event->answers()->with(['user', 'question'])->get();
        
        $groupedAnswers = [];
        foreach ($answers as $answer) {
            $userId = $answer->user_id;
            
            if (!isset($groupedAnswers[$userId])) {
                $groupedAnswers[$userId] = [
                    'user' => $answer->user,
                    'answers' => collect()
                ];
            }
            
            $groupedAnswers[$userId]['answers']->push($answer);
        }
        


        // Preparar datos para exportación
        $exportData = [];
        foreach ($groupedAnswers as $userId => $userData) {
            $exportData[] = [
                'user' => $userData['user'],
                'answers' => $userData['answers'],
                'submission_date' => $userData['answers']->first()->created_at
            ];
        }

        // Exportar según el formato solicitado
        if ($format === 'pdf') {
            $data = [
                'event' => $event,
                'questions' => $questions,
                'groupedAnswers' => $groupedAnswers
            ];
            
            return $this->exportService->exportToPDF(
                'admin.events.answers.export-pdf', 
                $data,
                "answers-event-{$event->id}.pdf"
            );
        } elseif ($format === 'excel') {
            $exportInstance = new EventAnswersExport($exportData, $questions);
            return $this->exportService->exportToExcel(
                $exportInstance, 
                "answers-event-{$event->id}.xlsx"
            );
        }
        
        return redirect()->back()->with('error', __('Invalid export format'));
    }

}