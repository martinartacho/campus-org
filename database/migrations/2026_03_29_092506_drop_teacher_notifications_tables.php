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
        // Eliminar tablas duplicadas de teacher notifications
        Schema::dropIfExists('teacher_notification_user');
        Schema::dropIfExists('teacher_notifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear tablas si se necesita rollback
        Schema::create('teacher_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notification_number')->unique();
            $table->string('title');
            $table->text('content');
            $table->string('type')->default('general');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('campus_courses')->onDelete('cascade');
            $table->string('recipient_type')->default('all');
            $table->json('recipient_ids')->nullable();
            $table->boolean('is_published')->default(false);
            $table->datetime('published_at')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->boolean('web_sent')->default(false);
            $table->boolean('push_sent')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['sender_id', 'course_id']);
            $table->index(['is_published', 'published_at']);
        });

        Schema::create('teacher_notification_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_notification_id')->constrained('teacher_notifications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('read')->default(false);
            $table->datetime('read_at')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->boolean('web_sent')->default(false);
            $table->boolean('push_sent')->default(false);
            $table->timestamps();
            
            $table->unique(['teacher_notification_id', 'user_id']);
            $table->index(['user_id', 'read']);
        });
    }
};
