<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use App\Models\EventQuestion;
use App\Models\EventAnswer;
use App\Models\EventQuestionTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Event::class);
        
        $events = Event::with('eventType')
            ->withCount(['questions', 'answers'])
            ->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $this->authorize('create', Event::class);
        $eventTypes = EventType::all();
        return view('admin.events.create', compact('eventTypes'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Event::class);
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'event_type_id' => 'nullable|exists:event_types,id',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'color' => 'nullable|string',
            'max_users' => 'nullable|integer|min:1',
            'visible' => 'boolean',
            'start_visible' => 'nullable|date',
            'end_visible' => 'nullable|date|after_or_equal:start_visible',
            'description' => 'nullable|string',
            'recurrence_type' => 'required|in:none,daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1',
            'recurrence_end_date' => 'nullable|date',
            'recurrence_count' => 'nullable|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Lógica para eventos recurrentes
            if ($validatedData['recurrence_type'] !== 'none') {
                $parentEvent = Event::create($validatedData);
                $parentEvent->parent_id = $parentEvent->id;
                $parentEvent->save();

                $start = Carbon::parse($validatedData['start']);
                $end = $validatedData['end'] ? Carbon::parse($validatedData['end']) : null;
                $duration = $end ? $start->diffInMinutes($end) : null;
                $count = $validatedData['recurrence_count'] ?? PHP_INT_MAX;
                $interval = $validatedData['recurrence_interval'] ?? 1;
                $endDate = $validatedData['recurrence_end_date'] ? Carbon::parse($validatedData['recurrence_end_date'])->endOfDay() : null;

                for ($i = 1; $i < $count; $i++) {
                    $newStart = clone $start;
                    $newEnd = $end ? clone $end : null;

                    switch ($validatedData['recurrence_type']) {
                        case 'daily':
                            $newStart->addDays($interval * $i);
                            if ($newEnd) $newEnd->addDays($interval * $i);
                            break;
                        case 'weekly':
                            $newStart->addWeeks($interval * $i);
                            if ($newEnd) $newEnd->addWeeks($interval * $i);
                            break;
                        case 'monthly':
                            $newStart->addMonths($interval * $i);
                            if ($newEnd) $newEnd->addMonths($interval * $i);
                            break;
                        case 'yearly':
                            $newStart->addYears($interval * $i);
                            if ($newEnd) $newEnd->addYears($interval * $i);
                            break;
                    }

                    if ($endDate && $newStart->gt($endDate)) {
                        break;
                    }

                    Event::create([
                        'title' => $validatedData['title'],
                        'event_type_id' => $validatedData['event_type_id'],
                        'start' => $newStart,
                        'end' => $newEnd,
                        'color' => $validatedData['color'],
                        'max_users' => $validatedData['max_users'],
                        'visible' => $validatedData['visible'] ?? false,
                        'start_visible' => $validatedData['start_visible'],
                        'end_visible' => $validatedData['end_visible'],
                        'description' => $validatedData['description'],
                        'parent_id' => $parentEvent->id,
                        'recurrence_type' => 'none',
                        'recurrence_interval' => null,
                        'recurrence_end_date' => null,
                        'recurrence_count' => null,
                    ]);
                }

                DB::commit();
                return redirect()->route('admin.events.index')->with('success', 'Eventos recurrentes creados con éxito.');
            } else {
                // Lógica para un evento simple
                Event::create($validatedData);
                DB::commit();
                return redirect()->route('admin.events.index')->with('success', 'Evento creado con éxito.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating event: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating event: ' . $e->getMessage());
        }
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        $eventTypes = EventType::all();
        return view('admin.events.edit', compact('event', 'eventTypes'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'color' => 'nullable|string|max:255',
            'max_users' => 'nullable|integer|min:1',
            'visible' => 'boolean',
            'start_visible' => 'nullable|date',
            'end_visible' => 'nullable|date|after_or_equal:start_visible',
            'event_type_id' => 'nullable|exists:event_types,id',
            'recurrence_type' => 'required|in:none,daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1',
            'recurrence_end_date' => 'nullable|date|after_or_equal:start',
            'recurrence_count' => 'nullable|integer|min:0',
            'update_scope' => 'nullable|in:one,future'
        ]);
        
        $validated['visible'] = (bool)($validated['visible'] ?? false);

        try {
            DB::beginTransaction();
            
            // Si el evento no es recurrente, simplemente lo actualizamos
            if ($event->recurrence_type === 'none' && $event->parent_id === null) {
                $event->update($validated);
            } else {
                // Si es un evento recurrente, aplicamos la lógica de ámbito
                $scope = $request->input('update_scope', 'one');
                $parentId = $event->parent_id ?? $event->id;
                $baseEvent = Event::find($parentId);

                if ($scope === 'one') {
                    // Solo actualiza el evento actual
                    $event->update($validated);
                } elseif ($scope === 'future') {
                    // Actualiza este evento y todos los futuros
                    $futureEvents = Event::where('parent_id', $parentId)
                                         ->where('start', '>=', $event->start)
                                         ->get();
                    
                    // Recalcular las fechas para cada evento futuro
                    $originalStart = Carbon::parse($event->start);
                    $originalEnd = $event->end ? Carbon::parse($event->end) : null;
                    $originalStartVisible = $event->start_visible ? Carbon::parse($event->start_visible) : null;
                    $originalEndVisible = $event->end_visible ? Carbon::parse($event->end_visible) : null;

                    foreach ($futureEvents as $futureEvent) {
                        $startDiff = $originalStart->diffInMinutes(Carbon::parse($futureEvent->start));
                        $endDiff = $originalEnd ? $originalEnd->diffInMinutes(Carbon::parse($futureEvent->end)) : null;
                        
                        $futureEvent->start = Carbon::parse($validated['start'])->addMinutes($startDiff);
                        $futureEvent->end = $validated['end'] ? Carbon::parse($validated['end'])->addMinutes($endDiff) : null;
                        
                        if ($originalStartVisible) {
                            $startVisibleDiff = $originalStartVisible->diffInMinutes(Carbon::parse($futureEvent->start_visible));
                            $futureEvent->start_visible = Carbon::parse($validated['start_visible'])->addMinutes($startVisibleDiff);
                        }
                        
                        if ($originalEndVisible) {
                            $endVisibleDiff = $originalEndVisible->diffInMinutes(Carbon::parse($futureEvent->end_visible));
                            $futureEvent->end_visible = Carbon::parse($validated['end_visible'])->addMinutes($endVisibleDiff);
                        }
                        
                        $futureEvent->title = $validated['title'];
                        $futureEvent->description = $validated['description'];
                        $futureEvent->color = $validated['color'];
                        $futureEvent->max_users = $validated['max_users'];
                        $futureEvent->visible = $validated['visible'];
                        $futureEvent->event_type_id = $validated['event_type_id'];
                        
                        $futureEvent->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.events.index')
                ->with('success', __('site.Event updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating event: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', __('site.Error updating event: :message', ['message' => $e->getMessage()]));
        }
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        $deleted = [];
        $skipped = [];

        try {
            DB::transaction(function () use ($event, &$deleted, &$skipped) {
                // Si es evento recurrente: intentamos borrar sus hijos (solo los que no tengan respuestas)
                if ($event->recurrence_type !== 'none') {
                    // Obtener todos los hijos directos
                    $children = Event::where('parent_id', $event->id)->get();

                    foreach ($children as $child) {
                        $hasAnswers = EventAnswer::where('event_id', $child->id)->exists();

                        if (!$hasAnswers) {
                            $child->delete();
                            $deleted[] = $child->id;
                        } else {
                            $skipped[] = $child->id;
                        }
                    }

                    // Comprobamos si quedan hijos (aquellos que tenían respuestas)
                    $remainingChildren = Event::where('parent_id', $event->id)->count();

                    // Solo borrar el padre si:
                    // 1) no tiene respuestas, y 2) no quedan hijos (evitamos orfanear eventos)
                    $parentHasAnswers = EventAnswer::where('event_id', $event->id)->exists();
                    if (!$parentHasAnswers && $remainingChildren === 0) {
                        $event->delete();
                        $deleted[] = $event->id;
                    } else {
                        // No podemos borrar el padre (bien porque tiene respuestas, bien porque quedan hijos con respuestas)
                        $skipped[] = $event->id;
                    }
                } else {
                    // Evento sin recurrencia: borrar solo si no tiene respuestas
                    $hasAnswers = EventAnswer::where('event_id', $event->id)->exists();
                    if (!$hasAnswers) {
                        $event->delete();
                        $deleted[] = $event->id;
                    } else {
                        $skipped[] = $event->id;
                    }
                }
            });
        } catch (\Throwable $e) {
            // Manejo de error: rollback automático por la transacción
            return redirect()->route('admin.events.index')
                ->with('error', 'Error al eliminar el evento: ' . $e->getMessage());
        }

        // Construir mensaje para el usuario
        if (count($deleted) > 0 && count($skipped) === 0) {
            $msg = 'Eventos eliminados correctamente: ' . implode(', ', $deleted) . '.';
            return redirect()->route('admin.events.index')->with('success', $msg);
        }

        if (count($deleted) > 0 && count($skipped) > 0) {
            $msg = 'Se eliminaron algunos eventos: ' . implode(', ', $deleted)
                . '. No se eliminaron (contienen respuestas o quedan hijos): ' . implode(', ', $skipped) . '.';
            return redirect()->route('ademin.events.index')->with('warning', $msg);
        }

        // Ningún borrado
        $msg = 'No se eliminaron eventos porque contienen respuestas.';
        return redirect()->route('admin.events.index')->with('error', $msg);
    }

    public function calendar()
    {
        $this->authorize('viewAny', Event::class);
        return view('admin.events.calendar');
    }
    
    public function calendarData()
    {
        try {
            $this->authorize('viewAny', Event::class);
            
            $events = Event::with('eventType')
                ->where('visible', true)
                ->where(function($query) {
                    $query->whereNull('start_visible')
                        ->orWhere('start_visible', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('end_visible')
                        ->orWhere('end_visible', '>=', now());
                })
                ->get();
                
            return response()->json($events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start->toIso8601String(),
                    'end' => $event->end ? $event->end->toIso8601String() : null,
                    'color' => $event->color ?? ($event->eventType->color ?? '#3c8dbc'),
                    'url' => route('admin.events.edit', $event->id),
                    'allDay' => !$event->end || $event->start->isSameDay($event->end),
                ];
            }));
        } catch (\Exception $e) {
            Log::error('Error fetching calendar data: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}