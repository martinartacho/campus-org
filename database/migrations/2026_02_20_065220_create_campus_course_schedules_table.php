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
        Schema::create('campus_course_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('campus_courses')->cascadeOnDelete();
            $table->foreignId('space_id')->constrained('campus_spaces')->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained('campus_time_slots')->cascadeOnDelete();
            $table->enum('semester', ['1Q', '2Q']);
            $table->enum('status', ['assigned', 'pending', 'conflict'])->default('pending');
            $table->integer('session_count')->default(12);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['space_id', 'time_slot_id', 'semester'], 'unique_space_time_semester');
            $table->index(['semester', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campus_course_schedules');
    }
};
