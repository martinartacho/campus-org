<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
        $this->middleware('permission:notifications.send');
    }

    /**
     * Show notification creation form
     */
    public function create(): View
    {
        return view('admin.notifications.create');
    }

    /**
     * Store new notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:specific,role,roles,filtered',
            'recipient_ids' => 'required_if:recipient_type,specific|array',
            'recipient_role' => 'required_if:recipient_type,role|string',
            'recipient_roles' => 'required_if:recipient_type,roles|array',
            'filters' => 'nullable|array'
        ]);

        $options = [
            'type' => $request->type ?? 'general'
        ];

        switch ($request->recipient_type) {
            case 'specific':
                $this->notificationService->sendToUsers(
                    $request->recipient_ids,
                    $request->title,
                    $request->content,
                    $options
                );
                break;

            case 'role':
                $this->notificationService->sendToRole(
                    $request->recipient_role,
                    $request->title,
                    $request->content,
                    $options
                );
                break;

            case 'roles':
                $this->notificationService->sendToRoles(
                    $request->recipient_roles,
                    $request->title,
                    $request->content,
                    $options
                );
                break;

            case 'filtered':
                $this->notificationService->sendToFiltered(
                    $request->filters ?? [],
                    $request->title,
                    $request->content,
                    $options
                );
                break;
        }

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notificació enviada correctament.');
    }

    /**
     * Show notification dashboard
     */
    public function index(): View
    {
        $stats = $this->notificationService->getNotificationStats();
        
        return view('admin.notifications.index', compact('stats'));
    }

    /**
     * Get users for AJAX requests
     */
    public function getUsers(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->take(20)->get(['id', 'name', 'email']);

        return response()->json($users);
    }
}
