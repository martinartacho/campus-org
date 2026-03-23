<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'notificationPreferences' => $this->getNotificationPreferences(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateLanguage(Request $request)
    {
        $request->validate([
            'language' => ['required', 'in:en,es,ca'],
        ]);

        // Guardar preferencia de usuario en JSON
        auth()->user()->setLanguage($request->language);

        // Actualizar el locale para la sesión actual
        app()->setLocale($request->language);
        session()->put('locale', $request->language);

        return redirect()->route('profile.edit')->with('status', 'language-updated');
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'notifications_email_enabled' => 'nullable|boolean',
            'notifications_web_enabled' => 'nullable|boolean',
            'notifications_support_email' => 'nullable|boolean',
            'notifications_support_web' => 'nullable|boolean',
            'notifications_department_email' => 'nullable|boolean',
            'notifications_department_web' => 'nullable|boolean',
            'notifications_admin_email' => 'nullable|boolean',
            'notifications_admin_web' => 'nullable|boolean',
            'notifications_frequency' => 'required|in:immediate,daily,weekly',
        ]);

        $user = auth()->user();
        $preferences = $user->preferences();

        // Update notification preferences
        $notificationPrefs = [
            'email_enabled' => $request->boolean('notifications_email_enabled', true),
            'web_enabled' => $request->boolean('notifications_web_enabled', true),
            'support_email' => $request->boolean('notifications_support_email', true),
            'support_web' => $request->boolean('notifications_support_web', true),
            'department_email' => $request->boolean('notifications_department_email', true),
            'department_web' => $request->boolean('notifications_department_web', true),
            'admin_email' => $request->boolean('notifications_admin_email', true),
            'admin_web' => $request->boolean('notifications_admin_web', true),
            'frequency' => $request->notifications_frequency,
        ];

        $preferences->updateNotificationPreferences($notificationPrefs);

        return redirect()->route('profile.edit')->with('status', 'notifications-updated');
    }

    /**
     * Get notification preferences for the current user
     */
    private function getNotificationPreferences()
    {
        $user = auth()->user();
        $preferences = $user->preferences();
        
        if ($preferences) {
            $prefs = $preferences->getNotificationPreferences();
            
            // Map JSON keys to form field names
            return [
                'notifications_email_enabled' => $prefs['email_enabled'] ?? true,
                'notifications_web_enabled' => $prefs['web_enabled'] ?? true,
                'notifications_support_email' => $prefs['support_email'] ?? true,
                'notifications_support_web' => $prefs['support_web'] ?? true,
                'notifications_department_email' => $prefs['department_email'] ?? true,
                'notifications_department_web' => $prefs['department_web'] ?? true,
                'notifications_admin_email' => $prefs['admin_email'] ?? true,
                'notifications_admin_web' => $prefs['admin_web'] ?? true,
                'notifications_frequency' => $prefs['frequency'] ?? 'immediate',
            ];
        }
        
        // Default values
        return [
            'notifications_email_enabled' => true,
            'notifications_web_enabled' => true,
            'notifications_support_email' => true,
            'notifications_support_web' => true,
            'notifications_department_email' => true,
            'notifications_department_web' => true,
            'notifications_admin_email' => true,
            'notifications_admin_web' => true,
            'notifications_frequency' => 'immediate',
        ];
    }

}
