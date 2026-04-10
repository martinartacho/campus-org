<?php

namespace App\Http\Controllers;

use App\Models\TaskBoard;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskBoardController extends Controller
{
    /**
     * Display a listing of the resource (API).
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $user = Auth::user();
        $boards = TaskBoard::visibleTo($user)
            ->with(['lists', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($boards);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:course,team,global,department',
            'entity_id' => 'nullable|integer',
            'visibility' => 'required|in:public,team,private',
        ]);

        $board = TaskBoard::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        // Create default lists
        $board->createDefaultLists();

        // Load relationships
        $board->load(['lists', 'creator']);

        return response()->json($board, 201);
    }

    /**
     * Display the specified resource (API).
     */
    public function apiShow(TaskBoard $board): JsonResponse
    {
        $this->authorize('view', $board);
        
        $board->load([
            'lists' => function ($query) {
                $query->with(['tasks' => function ($taskQuery) {
                    $taskQuery->with(['assignedUser', 'creator'])
                             ->orderBy('order_in_list');
                }]);
            },
            'creator'
        ]);

        return response()->json($board);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskBoard $board): JsonResponse
    {
        $this->authorize('update', $board);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'sometimes|required|in:public,team,private',
        ]);

        $board->update($validated);
        $board->load('creator');

        return response()->json($board);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskBoard $board): JsonResponse
    {
        $this->authorize('delete', $board);
        
        $board->delete();

        return response()->json(null, 204);
    }

    /**
     * Get board statistics.
     */
    public function statistics(TaskBoard $board): JsonResponse
    {
        $this->authorize('view', $board);

        $stats = [
            'total_tasks' => $board->tasks()->count(),
            'completed_tasks' => $board->tasks()->where('status', 'completed')->count(),
            'pending_tasks' => $board->tasks()->where('status', 'pending')->count(),
            'in_progress_tasks' => $board->tasks()->where('status', 'in_progress')->count(),
            'overdue_tasks' => $board->tasks()->overdue()->count(),
            'tasks_by_priority' => [
                'low' => $board->tasks()->where('priority', 'low')->count(),
                'medium' => $board->tasks()->where('priority', 'medium')->count(),
                'high' => $board->tasks()->where('priority', 'high')->count(),
                'urgent' => $board->tasks()->where('priority', 'urgent')->count(),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Show the form for creating a new board.
     */
    public function create()
    {
        return view('tasks.create-board');
    }

    /**
     * Show the form for editing the specified board.
     */
    public function edit(TaskBoard $board)
    {
        $this->authorize('update', $board);
        return view('tasks.edit-board', compact('board'));
    }

    /**
     * Display the boards listing page.
     */
    public function index()
    {
        return view('tasks.index');
    }

    /**
     * Display the specified board.
     */
    public function show(TaskBoard $board)
    {
        $this->authorize('view', $board);
        return view('tasks.board', compact('board'));
    }
}
