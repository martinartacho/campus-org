<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventQuestion;
use App\Models\EventAnswer;
use App\Models\EventQuestionTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventQuestionController extends Controller
{
    /**
     * Display a listing of the questions for an event.
     */
    public function index(Event $event)
    {
        $this->authorize('viewAny', EventQuestion::class);
        $questions = $event->questions;
        return view('admin.events.questions.index', compact('event', 'questions'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create(Event $event)
    {
        $this->authorize('create', EventQuestion::class);
        $templates = EventQuestionTemplate::where('is_template', true)->get();
        return view('admin.events.questions.create', compact('event', 'templates'));
    }
    
    /**
     * Store a newly created question.
     */
    public function store(Request $request, Event $event)
    {
        $this->authorize('create', EventQuestion::class);
        
        $validated = $request->validate([
            'question' => 'required|string|max:1000',
            'type' => 'required|in:text,single,multiple',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'required' => 'boolean',
        ]);
    
        if (isset($validated['options'])) {
            $validated['options'] = array_filter($validated['options'], fn($option) => !empty(trim($option)));
            if (empty($validated['options'])) {
                $validated['options'] = null;
            }
        }
    
        try {
            DB::beginTransaction();
    
            $newQuestion = $event->questions()->create($validated);
    
            if ($event->recurrence_type !== 'none' || $event->parent_id !== null) {
                $parentId = $event->parent_id ?? $event->id;
                $futureEvents = Event::where('parent_id', $parentId)
                                     ->where('start', '>', $event->start) // Solo eventos futuros, no el actual
                                     ->get();
    
                foreach ($futureEvents as $futureEvent) {
                    if (!$futureEvent->questions()->where('question', $validated['question'])->exists()) {
                         $futureEvent->questions()->create($validated);
                    }
                }
            }
    
            DB::commit();
            return redirect()->route('admin.events.questions.index', $event)
                ->with('success', __('site.Question created successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing question: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error storing question: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified question.
     */
    public function update(Request $request, Event $event, EventQuestion $question)
    {
        $this->authorize('update', $question);
        
        $validated = $request->validate([
            'question' => 'required|string|max:1000',
            'type' => 'required|in:text,single,multiple',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'required' => 'boolean',
            'update_scope' => 'nullable|in:one,future'
        ]);

        if (isset($validated['options'])) {
            $validated['options'] = array_filter($validated['options'], fn($option) => !empty(trim($option)));
            if (empty($validated['options'])) {
                $validated['options'] = null;
            }
        }
        
        $scope = $request->input('update_scope', 'one');

        try {
            DB::beginTransaction();

            if ($scope === 'one') {
                $question->update($validated);
            } elseif ($scope === 'future') {
                $parentId = $event->parent_id ?? $event->id;

                $futureEvents = Event::where('parent_id', $parentId)
                                     ->where('start', '>=', $event->start)
                                     ->get();

                foreach ($futureEvents as $futureEvent) {
                    $questionToUpdate = $futureEvent->questions()->where('question', $question->question)->first();
                    if ($questionToUpdate) {
                        $questionToUpdate->update($validated);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.events.questions.index', $event)
                ->with('success', __('site.Question updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating question: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating question: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Event $event, EventQuestion $question)
    {
        $this->authorize('update', $question);
        $templates = EventQuestionTemplate::where('is_template', true)->get();
        
        // Determinar si es un evento recurrente
        $isRecurring = ($event->recurrence_type !== 'none' || $event->parent_id !== null);
        
        return view('admin.events.questions.edit', compact('event', 'question', 'templates', 'isRecurring'));
    }

    /**
     * Remove the specified question.
     */
    public function destroy(Event $event, EventQuestion $question, Request $request)
    {
        $this->authorize('delete', $question);
        
        try {
            DB::beginTransaction();
            
            // 1. Verificar si la pregunta tiene respuestas
            if (EventAnswer::where('event_question_id', $question->id)->exists()) {
                 DB::rollBack();
                 return redirect()->back()->with('error', __('site.Cannot delete question with answers.'));
            }

            // 2. Determinar el ámbito de eliminación
            $deleteScope = $request->input('delete_scope', 'one');

            // 3. Aplicar la lógica de eliminación según el ámbito
            if ($deleteScope === 'one') {
                $question->delete();
            } elseif ($deleteScope === 'future') {
                 // Buscar todas las preguntas futuras de eventos recurrentes relacionados
                $parentId = $event->parent_id ?? $event->id;
                $futureEvents = Event::where('parent_id', $parentId)
                                     ->where('start', '>=', $event->start)
                                     ->get();
                
                foreach ($futureEvents as $futureEvent) {
                    $questionToDelete = $futureEvent->questions()->where('question', $question->question)->first();
                    if ($questionToDelete) {
                        $questionToDelete->delete();
                    }
                }
            }
            
            DB::commit();

            return redirect()->route('admin.events.questions.index', $event)
                ->with('success', __('site.Question deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting question: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('site.Error deleting question: :message', ['message' => $e->getMessage()]));
        }
    }
}