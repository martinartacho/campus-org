<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create new table for user preferences
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('preferences')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });

        // 2. Migrate existing data
        $this->migrateExistingSettings();

        // 3. Update User model to use new relationship (manual step)
        // 4. Drop old table (optional, after verification)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate old table structure
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value');
            $table->timestamps();
            
            $table->unique(['user_id', 'key']);
        });

        // Migrate data back (simplified - you may need to enhance this)
        $this->migrateBackToOldFormat();

        // Drop new table
        Schema::dropIfExists('user_preferences');
    }

    /**
     * Migrate existing user_settings to new JSON format
     */
    private function migrateExistingSettings(): void
    {
        // Get all users with settings
        $usersWithSettings = DB::table('user_settings')
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        foreach ($usersWithSettings as $userId) {
            // Get all settings for this user
            $settings = DB::table('user_settings')
                ->where('user_id', $userId)
                ->get();

            $preferences = [
                'notifications' => [
                    'email_enabled' => true,
                    'web_enabled' => true,
                    'support_email' => true,
                    'support_web' => true,
                    'department_email' => true,
                    'department_web' => true,
                    'admin_email' => true,
                    'admin_web' => true,
                    'frequency' => 'immediate',
                ],
                'ui' => [
                    'language' => 'ca',
                ]
            ];

            // Convert existing settings
            foreach ($settings as $setting) {
                $value = $this->convertValue($setting->value);
                
                // Map old keys to new structure
                switch ($setting->key) {
                    case 'notifications_email_enabled':
                        $preferences['notifications']['email_enabled'] = $value;
                        break;
                    case 'notifications_web_enabled':
                        $preferences['notifications']['web_enabled'] = $value;
                        break;
                    case 'notifications_support_email':
                        $preferences['notifications']['support_email'] = $value;
                        break;
                    case 'notifications_support_web':
                        $preferences['notifications']['support_web'] = $value;
                        break;
                    case 'notifications_department_email':
                        $preferences['notifications']['department_email'] = $value;
                        break;
                    case 'notifications_department_web':
                        $preferences['notifications']['department_web'] = $value;
                        break;
                    case 'notifications_admin_email':
                        $preferences['notifications']['admin_email'] = $value;
                        break;
                    case 'notifications_admin_web':
                        $preferences['notifications']['admin_web'] = $value;
                        break;
                    case 'notifications_frequency':
                        $preferences['notifications']['frequency'] = $value;
                        break;
                    case 'language':
                        $preferences['ui']['language'] = $value;
                        break;
                }
            }

            // Insert into new table
            DB::table('user_preferences')->insert([
                'user_id' => $userId,
                'preferences' => json_encode($preferences),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Convert various value formats to proper types
     */
    private function convertValue($value): mixed
    {
        // Handle boolean-like values
        if (in_array($value, ['true', '1', 1, true], true)) {
            return true;
        } elseif (in_array($value, ['false', '0', 0, false], true)) {
            return false;
        }

        // Return as-is for strings
        return $value;
    }

    /**
     * Migrate back to old format (simplified)
     */
    private function migrateBackToOldFormat(): void
    {
        $preferences = DB::table('user_preferences')->get();

        foreach ($preferences as $pref) {
            $data = json_decode($pref->preferences, true);
            
            if (isset($data['notifications'])) {
                foreach ($data['notifications'] as $key => $value) {
                    $settingKey = 'notifications_' . $key;
                    
                    DB::table('user_settings')->insert([
                        'user_id' => $pref->user_id,
                        'key' => $settingKey,
                        'value' => is_bool($value) ? ($value ? 'true' : 'false') : $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            if (isset($data['ui'])) {
                foreach ($data['ui'] as $key => $value) {
                    DB::table('user_settings')->insert([
                        'user_id' => $pref->user_id,
                        'key' => $key,
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
};
