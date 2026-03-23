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
        // Drop the old user_settings table since we've migrated to JSON
        Schema::dropIfExists('user_settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the old table structure if needed
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value');
            $table->timestamps();
            
            $table->unique(['user_id', 'key']);
        });
    }
};
