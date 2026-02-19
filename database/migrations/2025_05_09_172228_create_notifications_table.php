<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('sender_id')->constrained('users');
            $table->enum('recipient_type', ['all', 'role', 'specific'])->default('all');
            $table->string('recipient_role')->nullable();
            $table->json('recipient_ids')->nullable(); // Para usuarios específicos
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->boolean('web_sent')->default(false);
            $table->boolean('push_sent')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('notification_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps(); 
            
            $table->unique(['notification_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_user');
        Schema::dropIfExists('notifications');
    }
};
