<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notification_user', function (Blueprint $table) {
            // Boolean flags for each channel
            $table->boolean('push_sent')->default(false)->after('read_at');
            $table->boolean('email_sent')->default(false)->after('push_sent');
            $table->boolean('web_sent')->default(false)->after('email_sent');
            
            // Timestamps for each channel
            $table->timestamp('push_sent_at')->nullable()->after('web_sent');
            $table->timestamp('email_sent_at')->nullable()->after('push_sent_at');
            $table->timestamp('web_sent_at')->nullable()->after('email_sent_at');
            
            // Indexes for performance
            $table->index(['push_sent', 'push_sent_at']);
            $table->index(['email_sent', 'email_sent_at']);
            $table->index(['web_sent', 'web_sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_user', function (Blueprint $table) {
            $table->dropIndex(['push_sent', 'push_sent_at']);
            $table->dropIndex(['email_sent', 'email_sent_at']);
            $table->dropIndex(['web_sent', 'web_sent_at']);
            
            $table->dropColumn([
                'push_sent',
                'email_sent', 
                'web_sent',
                'push_sent_at',
                'email_sent_at',
                'web_sent_at'
            ]);
        });
    }
};
