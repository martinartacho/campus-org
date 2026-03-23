<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to specific users
     */
    public function sendToUsers(array $userIds, string $title, string $content, array $options = [])
    {
        $notification = Notification::create([
            'title' => $title,
            'content' => $content,
            'type' => $options['type'] ?? 'general',
            'sender_id' => Auth::id(),
            'recipient_type' => 'specific',
            'recipient_ids' => $userIds,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->sendEmailNotifications($notification, $userIds);
        
        return $notification;
    }

    /**
     * Send notification to users by role
     */
    public function sendToRole(string $role, string $title, string $content, array $options = [])
    {
        $notification = Notification::create([
            'title' => $title,
            'content' => $content,
            'type' => $options['type'] ?? 'general',
            'sender_id' => Auth::id(),
            'recipient_type' => 'role',
            'recipient_role' => $role,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $roleUsers = User::whereHas('roles', function($query) use ($role) {
            $query->where('name', $role);
        })->get();

        $this->sendEmailNotifications($notification, $roleUsers->pluck('id')->toArray());
        
        return $notification;
    }

    /**
     * Send notification to multiple roles
     */
    public function sendToRoles(array $roles, string $title, string $content, array $options = [])
    {
        $notification = Notification::create([
            'title' => $title,
            'content' => $content,
            'type' => $options['type'] ?? 'general',
            'sender_id' => Auth::id(),
            'recipient_type' => 'roles',
            'recipient_role' => implode(',', $roles),
            'is_published' => true,
            'published_at' => now(),
        ]);

        $roleUsers = User::whereHas('roles', function($query) use ($roles) {
            $query->whereIn('name', $roles);
        })->get();

        $this->sendEmailNotifications($notification, $roleUsers->pluck('id')->toArray());
        
        return $notification;
    }

    /**
     * Send notification to filtered users
     */
    public function sendToFiltered(array $filters, string $title, string $content, array $options = [])
    {
        $query = User::query();

        // Apply filters
        if (isset($filters['roles'])) {
            $query->whereHas('roles', function($q) use ($filters) {
                $q->whereIn('name', $filters['roles']);
            });
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        $users = $query->get();
        $userIds = $users->pluck('id')->toArray();

        $notification = Notification::create([
            'title' => $title,
            'content' => $content,
            'type' => $options['type'] ?? 'general',
            'sender_id' => Auth::id(),
            'recipient_type' => 'specific',
            'recipient_ids' => $userIds,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->sendEmailNotifications($notification, $userIds);
        
        return $notification;
    }

    /**
     * Send email notifications to users
     */
    private function sendEmailNotifications($notification, array $userIds)
    {
        $users = User::whereIn('id', $userIds)->get();
        
        foreach ($users as $user) {
            try {
                Mail::raw(
                    $notification->content,
                    function ($message) use ($user, $notification) {
                        $message->to($user->email)
                            ->subject($notification->title)
                            ->from(config('mail.from_address'), config('mail.from_name', 'UPG'));
                    }
                );
            } catch (\Exception $e) {
                Log::error('Error sending email to ' . $user->email . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats()
    {
        return [
            'total' => Notification::count(),
            'unread' => Notification::whereNull('read_at')->count(),
            'today' => Notification::whereDate('created_at', today())->count(),
            'this_week' => Notification::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];
    }
}
