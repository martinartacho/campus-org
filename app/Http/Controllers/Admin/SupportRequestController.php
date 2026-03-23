<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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
     * Send notification for support request
     */
    public function sendNotification(Request $request, SupportRequest $supportRequest): \Illuminate\Http\JsonResponse
    {
        try {
            // Create notification for user who submitted the request
            $recipientIds = $supportRequest->user_id ? [$supportRequest->user_id] : [auth()->id() ?? 1];
            
            $notification = Notification::create([
                'title' => "Actualització Sol·licitud #{$supportRequest->ticket_number}",
                'content' => ($supportRequest->user_id
                    ? "{$request->message}\n\nDetalls:\nNúmero de Ticket: {$supportRequest->ticket_number}\nEstat Actual: {$supportRequest->status_label}\nDepartament: {$this->getDepartmentName($supportRequest->department)}\n\nPodeu fer seguiment del vostre cas a través del número de ticket."
                    : "{$request->message}\n\nDetalls:\nNúmero de Ticket: {$supportRequest->ticket_number}\nEstat Actual: {$supportRequest->status_label}\nDepartament: {$this->getDepartmentName($supportRequest->department)}\n\nAquesta sol·licitud va ser tramitada per correu electrònic. Podeu fer seguiment del vostre cas a través del número de ticket."
                ),
                'type' => 'support',
                'sender_id' => auth()->id() ?? 1,
                'recipient_type' => 'specific',
                'recipient_ids' => $recipientIds,
                'is_published' => true,
                'published_at' => now(),
            ]);

            // Assign recipients to notification_user table
            $this->assignNotificationRecipients($notification, $recipientIds);

            // Send notification through all channels
            $this->sendNotificationChannels($notification);

            // If user is not registered and has valid email, send email notification
            if (!$supportRequest->user_id && !empty($supportRequest->email)) {
                try {
                    Mail::raw(
                        $notification->content,
                        function ($message) use ($supportRequest) {
                            $message->to($supportRequest->email)
                                ->subject("Actualització Sol·licitud #{$supportRequest->ticket_number}")
                                ->from(config('mail.from_address', 'noreply@upg.cat'), config('mail.from_name', 'UPG Campus'));
                        }
                    );
                } catch (\Exception $e) {
                    \Log::error('Error sending email notification: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Notificació enviada correctament'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending status notification: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error en enviar la notificació'
            ], 500);
        }
    }

    /**
     * Send notification through all channels (email, web, push)
     */
    private function sendNotificationChannels(Notification $notification)
    {
        try {
            // Send email channel
            $this->sendEmailChannel($notification);
            
            // Send web channel (already available via notification_user table)
            $this->sendWebChannel($notification);
            
        } catch (\Exception $e) {
            \Log::error('Error sending notification channels: ' . $e->getMessage());
        }
    }

    /**
     * Send email channel
     */
    private function sendEmailChannel(Notification $notification)
    {
        $recipients = $notification->recipients()->wherePivot('email_sent', false)->get();
        
        foreach ($recipients as $user) {
            try {
                // Check user's email notification preference
                if (!$user->wantsEmailNotification($notification->type)) {
                    \Log::info("User {$user->email} has disabled {$notification->type} email notifications");
                    continue;
                }

                // Verify email is valid
                if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                // Use Mailable class
                $mail = new \App\Mail\NotificationMail($notification, $user);
                
                Mail::to($user->email)->send($mail);
                
                // Mark as sent in pivot table
                $notification->recipients()->updateExistingPivot($user->id, [
                    'email_sent' => true,
                    'email_sent_at' => now(),
                ]);

            } catch (\Exception $e) {
                \Log::error("Error sending email to {$user->email}: " . $e->getMessage());
            }
        }
    }

    /**
     * Send web channel
     */
    private function sendWebChannel(Notification $notification)
    {
        $users = $notification->recipients()->wherePivot('web_sent', false)->get();

        foreach ($users as $user) {
            // Check user's web notification preference
            if (!$user->wantsWebNotification($notification->type)) {
                \Log::info("User {$user->email} has disabled {$notification->type} web notifications");
                continue;
            }

            // Mark as web sent (notification is already available in web)
            $notification->recipients()->updateExistingPivot($user->id, [
                'web_sent' => true,
                'web_sent_at' => now(),
            ]);
        }
    }

    /**
     * Assign recipients to notification_user table
     */
    private function assignNotificationRecipients(Notification $notification, array $recipientIds)
    {
        $notification->recipients()->syncWithPivotValues($recipientIds, [
            'email_sent' => false,
            'web_sent' => false,
            'push_sent' => false,
            'read' => false,
        ]);
    }

    /**
     * Bulk notify selected requests
     */
    public function bulkNotify(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:support_requests,id',
            'message' => 'required|string|max:1000',
        ]);

        try {
            $successCount = 0;
            $failedCount = 0;

            foreach ($request->request_ids as $requestId) {
                $supportRequest = SupportRequest::find($requestId);
                
                if (!$supportRequest) {
                    $failedCount++;
                    continue;
                }

                // Create notification for user who submitted the request
                $recipientIds = $supportRequest->user_id ? [$supportRequest->user_id] : [auth()->id() ?? 1];
                
                $notification = Notification::create([
                    'title' => "Actualització Sol·licitud #{$supportRequest->ticket_number}",
                    'content' => ($supportRequest->user_id
                        ? "{$request->message}\n\nDetalls:\nNúmero de Ticket: {$supportRequest->ticket_number}\nEstat Actual: {$supportRequest->status_label}\nDepartament: {$this->getDepartmentName($supportRequest->department)}\n\nPodeu fer seguiment del vostre cas a través del número de ticket."
                        : "{$request->message}\n\nDetalls:\nNúmero de Ticket: {$supportRequest->ticket_number}\nEstat Actual: {$supportRequest->status_label}\nDepartament: {$this->getDepartmentName($supportRequest->department)}\n\nAquesta sol·licitud va ser tramitada per correu electrònic. Podeu fer seguiment del vostre cas a través del número de ticket."
                    ),
                    'type' => 'support',
                    'sender_id' => auth()->id() ?? 1,
                    'recipient_type' => 'specific',
                    'recipient_ids' => $recipientIds,
                    'is_published' => true,
                    'published_at' => now(),
                ]);

                // Assign recipients to notification_user table
                $this->assignNotificationRecipients($notification, $recipientIds);

                // Send notification through all channels
                $this->sendNotificationChannels($notification);

                // If user is not registered and has valid email, send email notification
                if (!$supportRequest->user_id && !empty($supportRequest->email)) {
                    try {
                        Mail::raw(
                            $notification->content,
                            function ($message) use ($supportRequest) {
                                $message->to($supportRequest->email)
                                    ->subject("Actualització Sol·licitud #{$supportRequest->ticket_number}")
                                    ->from(config('mail.from_address', 'noreply@upg.cat'), config('mail.from_name', 'UPG Campus'));
                            }
                        );
                    } catch (\Exception $e) {
                        \Log::error('Error sending bulk email notification: ' . $e->getMessage());
                    }
                }

                $successCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$successCount} notificacions enviades correctament" . ($failedCount > 0 ? " ({$failedCount} errors)" : "")
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending bulk notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error en enviar les notificacions massives'
            ], 500);
        }
    }

    /**
     * Bulk delete selected requests
     */
    public function bulkDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:support_requests,id',
        ]);

        $deletedCount = 0;
        foreach ($request->request_ids as $requestId) {
            $supportRequest = SupportRequest::find($requestId);
            if ($supportRequest) {
                $supportRequest->delete();
                $deletedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} sol·licituds eliminades correctament"
        ]);
    }

    /**
     * Get department name in Catalan
     */
    protected function getDepartmentName($department)
    {
        $names = [
            'admin' => 'Administració',
            'junta' => 'Junta Directiva',
            'coordinacio' => 'Coordinació',
            'gestio' => 'Gestió',
            'comunicacio' => 'Comunicació',
            'secretaria' => 'Secretaria',
            'editor' => 'Editor',
            'support' => 'Suport',
            'technical' => 'Tècnic',
        ];

        return $names[$department] ?? $department;
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
