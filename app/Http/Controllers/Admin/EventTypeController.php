<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use Illuminate\Http\Request;

class EventTypeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', EventType::class);
        
        $eventTypes = EventType::all();
        return view('admin.event-types.index', compact('eventTypes'));
    }

    public function create()
    {
        $this->authorize('create', EventType::class);
        
        return view('admin.event-types.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', EventType::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'is_default' => 'boolean',
        ]);

        EventType::create($validated);

        return redirect()->route('admin.event-types.index')
            ->with('success', __('Event type created successfully.'));
    }

    public function edit(EventType $eventType)
    {
        $this->authorize('update', $eventType);
        
        return view('admin.event-types.edit', compact('eventType'));
    }

    public function update(Request $request, EventType $eventType)
    {
        $this->authorize('update', $eventType);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'is_default' => 'boolean',
        ]);

        $eventType->update($validated);

        return redirect()->route('admin.event-types.index')
            ->with('success', __('Event type updated successfully.'));
    }

    public function destroy(EventType $eventType)
    {
        $this->authorize('delete', $eventType);
        
        // Prevent deletion if it's used by events
        if ($eventType->events()->count() > 0) {
            return redirect()->route('admin.event-types.index')
                ->with('error', __('Cannot delete event type that is in use.'));
        }

        $eventType->delete();

        return redirect()->route('admin.event-types.index')
            ->with('success', __('Event type deleted successfully.'));
    }
}