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
        Schema::create('campus_teacher_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('campus_teachers')->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained('campus_time_slots')->cascadeOnDelete();
            $table->enum('semester', ['1Q', '2Q']);
            $table->boolean('is_available')->default(true);
            $table->json('preferences')->nullable(); // {"preferred_spaces": ["SA", "AM1"], "notes": "..."}
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['teacher_id', 'time_slot_id', 'semester'], 'unique_teacher_time_semester');
            $table->index(['semester', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campus_teacher_schedules');
    }
};
