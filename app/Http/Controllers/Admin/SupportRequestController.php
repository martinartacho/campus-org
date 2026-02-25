<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupportRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:support-requests.view');
    }

    /**
     * Display a listing of support requests.
     */
    public function index(Request $request): View
    {
        $query = SupportRequest::with(['user', 'resolvedBy'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%");
            });
        }

        $supportRequests = $query->paginate(15);

        // Estadísticas
        $stats = [
            'total' => SupportRequest::count(),
            'pending' => SupportRequest::where('status', 'pending')->count(),
            'in_progress' => SupportRequest::where('status', 'in_progress')->count(),
            'resolved' => SupportRequest::where('status', 'resolved')->count(),
            'critical' => SupportRequest::where('urgency', 'critical')->where('status', '!=', 'resolved')->count(),
        ];

        return view('admin.support-requests.index', compact('supportRequests', 'stats'));
    }

    /**
     * Display the specified support request.
     */
    public function show(SupportRequest $supportRequest): View
    {
        $supportRequest->load(['user', 'resolvedBy']);
        
        return view('admin.support-requests.show', compact('supportRequest'));
    }

    /**
     * Update the status of a support request.
     */
    public function updateStatus(Request $request, SupportRequest $supportRequest): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,closed',
            'resolution_notes' => 'required_if:status,resolved,closed|string|max:1000',
        ]);

        $supportRequest->update([
            'status' => $request->status,
            'resolution_notes' => $request->resolution_notes,
            'resolved_by' => $request->status === 'resolved' ? Auth::id() : $supportRequest->resolved_by,
            'resolved_at' => $request->status === 'resolved' ? now() : $supportRequest->resolved_at,
        ]);

        return redirect()
            ->route('admin.support-requests.show', $supportRequest)
            ->with('success', 'Estat actualitzat correctament.');
    }

    /**
     * Remove the specified support request.
     */
    public function destroy(SupportRequest $supportRequest): RedirectResponse
    {
        $supportRequest->delete();

        return redirect()
            ->route('admin.support-requests.index')
            ->with('success', 'Sol·licitud eliminada correctament.');
    }

    /**
     * Bulk update status for multiple requests.
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:support_requests,id',
            'status' => 'required|in:pending,in_progress,resolved,closed',
        ]);

        SupportRequest::whereIn('id', $request->request_ids)
            ->update([
                'status' => $request->status,
                'resolved_by' => $request->status === 'resolved' ? Auth::id() : null,
                'resolved_at' => $request->status === 'resolved' ? now() : null,
            ]);

        return redirect()
            ->route('admin.support-requests.index')
            ->with('success', count($request->request_ids) . ' sol·licituds actualitzades.');
    }
}
