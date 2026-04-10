<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\TaskActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'list_id' => 'required|exists:task_lists,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_role' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $task = Task::create([
            ...$validated,
            'created_by' => Auth::id(),
            'status' => 'pending',
            'order_in_list' => TaskList::find($validated['list_id'])->tasks()->count(),
        ]);

        // Log activity
        $task->logActivity('created', Auth::user());

        // Send notification if assigned
        if ($task->assigned_to && $task->assigned_to != Auth::id()) {
            // TODO: Implement notification system
        }

        return response()->json($task->load(['assignedUser', 'creator', 'list']), 201);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_role' => 'nullable|string',
            'priority' => 'sometimes|required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'sometimes|required|in:pending,in_progress,blocked,completed',
            'list_id' => 'sometimes|required|exists:task_lists,id',
        ]);

        $oldValues = $task->only(array_keys($validated));
        $task->update($validated);
        $newValues = $task->only(array_keys($validated));

        // Log activity for significant changes
        $changes = array_diff_assoc($newValues, $oldValues);
        if (!empty($changes)) {
            $action = $this->determineAction($changes);
            $task->logActivity($action, Auth::user(), $oldValues, $newValues);

            // Send notifications for important changes
            if (isset($changes['assigned_to']) && $changes['assigned_to'] != Auth::id()) {
                // TODO: Implement assignment notification
            }
        }

        return response()->json($task->load(['assignedUser', 'creator', 'list']));
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);
        
        $task->delete();

        return response()->json(null, 204);
    }

    /**
     * Move task to another list or reorder within list.
     */
    public function move(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'list_id' => 'required|exists:task_lists,id',
            'order' => 'required|integer|min:0',
        ]);

        $oldListId = $task->list_id;
        $oldOrder = $task->order_in_list;

        // Update task
        $task->update([
            'list_id' => $validated['list_id'],
            'order_in_list' => $validated['order'],
        ]);

        // Reorder tasks in old list if moved
        if ($oldListId != $validated['list_id']) {
            Task::where('list_id', $oldListId)
                ->where('order_in_list', '>', $oldOrder)
                ->decrement('order_in_list');
        }

        // Reorder tasks in new list
        Task::where('list_id', $validated['list_id'])
            ->where('id', '!=', $task->id)
            ->where('order_in_list', '>=', $validated['order'])
            ->increment('order_in_list');

        // Log activity
        $task->logActivity('updated', Auth::user(), [
            'list_id' => $oldListId,
            'order_in_list' => $oldOrder,
        ], [
            'list_id' => $validated['list_id'],
            'order_in_list' => $validated['order'],
        ]);

        return response()->json($task->load('list'));
    }

    /**
     * Add comment to task.
     */
    public function addComment(Request $request, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
        ]);

        // Log activity
        $task->logActivity('commented', Auth::user());

        // Notify task assigned user (if different from commenter)
        if ($task->assigned_to && $task->assigned_to != Auth::id()) {
            // TODO: Implement comment notification
        }

        return response()->json($comment->load('user'), 201);
    }

    /**
     * Upload attachment to task.
     */
    public function uploadAttachment(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $validated['file'];
        $path = $file->store('task-attachments', 'public');

        $attachment = $task->attachments()->create([
            'user_id' => Auth::id(),
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        // Log activity
        $task->logActivity('attachment_added', Auth::user());

        return response()->json($attachment->load('user'), 201);
    }

    /**
     * Download task attachment.
     */
    public function downloadAttachment(TaskAttachment $attachment): \Illuminate\Http\Response
    {
        $this->authorize('view', $attachment->task);

        if (!Storage::disk('public')->exists($attachment->path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($attachment->path, $attachment->filename);
    }

    /**
     * Get task activities.
     */
    public function activities(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $activities = $task->activities()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($activities);
    }

    /**
     * Determine the activity action based on changes.
     */
    private function determineAction(array $changes): string
    {
        if (isset($changes['assigned_to'])) {
            return $changes['assigned_to'] ? 'assigned' : 'unassigned';
        }
        
        if (isset($changes['status'])) {
            return $changes['status'] === 'completed' ? 'completed' : 'status_changed';
        }
        
        if (isset($changes['priority'])) {
            return 'priority_changed';
        }
        
        if (isset($changes['due_date'])) {
            return 'due_date_changed';
        }
        
        return 'updated';
    }
}
