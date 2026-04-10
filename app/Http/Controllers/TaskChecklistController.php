<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskChecklist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskChecklistController extends Controller
{
    /**
     * Get all checklists for a task.
     */
    public function index(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $checklists = $task->checklists()->orderBy('order')->get();

        return response()->json($checklists);
    }

    /**
     * Store a new checklist item.
     */
    public function store(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'sometimes|integer|min:0',
        ]);

        // Set default order if not provided
        if (!isset($validated['order'])) {
            $maxOrder = $task->checklists()->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        $checklist = $task->checklists()->create($validated);

        return response()->json($checklist, 201);
    }

    /**
     * Update a checklist item.
     */
    public function update(Request $request, TaskChecklist $checklist): JsonResponse
    {
        $this->authorize('update', $checklist->task);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'is_completed' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
        ]);

        $checklist->update($validated);

        return response()->json($checklist);
    }

    /**
     * Delete a checklist item.
     */
    public function destroy(TaskChecklist $checklist): JsonResponse
    {
        $this->authorize('update', $checklist->task);

        $checklist->delete();

        return response()->json(null, 204);
    }

    /**
     * Toggle checklist item completion.
     */
    public function toggle(TaskChecklist $checklist): JsonResponse
    {
        $this->authorize('update', $checklist->task);

        $checklist->toggle();

        return response()->json($checklist);
    }

    /**
     * Reorder checklist items.
     */
    public function reorder(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:task_checklists,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            TaskChecklist::where('id', $item['id'])
                        ->where('task_id', $task->id)
                        ->update(['order' => $item['order']]);
        }

        return response()->json(['message' => 'Checklist items reordered successfully']);
    }
}
