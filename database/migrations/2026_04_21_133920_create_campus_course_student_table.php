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
        Schema::create('campus_course_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('campus_courses')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('campus_students')->onDelete('cascade');
            $table->foreignId('registration_id')->nullable()->constrained('campus_registrations')->onDelete('cascade');
            $table->enum('status', ['active', 'completed', 'dropped', 'suspended'])->default('active');
            $table->date('enrollment_date');
            $table->date('completion_date')->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicates
            $table->unique(['course_id', 'student_id'], 'course_student_unique');
            
            // Indexes for performance
            $table->index(['status', 'enrollment_date']);
            $table->index(['course_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campus_course_student');
    }
};
