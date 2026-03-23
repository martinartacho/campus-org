<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'preferences',
    ];

    protected $casts = [
        'preferences' => 'array',
    ];

    /**
     * Get the user that owns the preferences
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a specific preference value
     */
    public function getPreference(string $key, mixed $default = null): mixed
    {
        $preferences = $this->preferences;
        
        // Support dot notation for nested keys
        $keys = explode('.', $key);
        $value = $preferences;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }

    /**
     * Set a specific preference value
     */
    public function setPreference(string $key, mixed $value): void
    {
        $preferences = $this->preferences ?? [];
        
        // Support dot notation for nested keys
        $keys = explode('.', $key);
        $current = &$preferences;
        
        foreach ($keys as $k) {
            if (!isset($current[$k]) || !is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        
        $current = $value;
        
        $this->preferences = $preferences;
        $this->save();
    }

    /**
     * Get notification preferences
     */
    public function getNotificationPreferences(): array
    {
        return $this->getPreference('notifications', [
            'email_enabled' => true,
            'web_enabled' => true,
            'support_email' => true,
            'support_web' => true,
            'department_email' => true,
            'department_web' => true,
            'admin_email' => true,
            'admin_web' => true,
            'frequency' => 'immediate',
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(array $preferences): void
    {
        $current = $this->preferences ?? [];
        $current['notifications'] = array_merge($this->getNotificationPreferences(), $preferences);
        
        $this->preferences = $current;
        $this->save();
    }

    /**
     * Check if user wants email notifications for a specific type
     */
    public function wantsEmailNotification(string $type): bool
    {
        $key = "notifications.{$type}_email";
        return $this->getPreference($key, true);
    }

    /**
     * Check if user wants web notifications for a specific type
     */
    public function wantsWebNotification(string $type): bool
    {
        $key = "notifications.{$type}_web";
        return $this->getPreference($key, true);
    }

    /**
     * Get notification frequency
     */
    public function getNotificationFrequency(): string
    {
        return $this->getPreference('notifications.frequency', 'immediate');
    }
}
