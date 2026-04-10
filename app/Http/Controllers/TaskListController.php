<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\TaskBoard;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskListController extends Controller
{
    /**
     * Store a newly created list in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:task_boards,id',
            'name' => 'required|string|max:255',
            'color' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'order' => 'sometimes|integer|min:0',
        ]);

        $this->authorize('update', TaskBoard::find($validated['board_id']));

        // Set default order if not provided
        if (!isset($validated['order'])) {
            $maxOrder = TaskList::where('board_id', $validated['board_id'])->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        // Set default color if not provided
        if (!isset($validated['color'])) {
            $validated['color'] = '#6B7280';
        }

        $list = TaskList::create($validated);

        return response()->json($list->load('board'), 201);
    }

    /**
     * Update the specified list in storage.
     */
    public function update(Request $request, TaskList $list): JsonResponse
    {
        $this->authorize('update', $list->board);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'order' => 'sometimes|integer|min:0',
        ]);

        $list->update($validated);

        return response()->json($list->load('board'));
    }

    /**
     * Remove the specified list from storage.
     */
    public function destroy(TaskList $list): JsonResponse
    {
        $this->authorize('delete', $list->board);
        
        // Check if list has tasks
        if ($list->tasks()->exists()) {
            return response()->json([
                'message' => 'No es pot eliminar una llista que conté tasques'
            ], 422);
        }
        
        $list->delete();

        return response()->json(null, 204);
    }

    /**
     * Reorder lists within a board.
     */
    public function reorder(Request $request, TaskBoard $board): JsonResponse
    {
        $this->authorize('update', $board);

        $validated = $request->validate([
            'lists' => 'required|array',
            'lists.*.id' => 'required|exists:task_lists,id',
            'lists.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['lists'] as $listData) {
            TaskList::where('id', $listData['id'])
                   ->where('board_id', $board->id)
                   ->update(['order' => $listData['order']]);
        }

        return response()->json(['message' => 'Lists reordered successfully']);
    }
}
