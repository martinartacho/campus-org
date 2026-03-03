<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_course_student', function (Blueprint $table) {
            $table->id();
            
            // Relacions principals
            $table->foreignId('student_id')->constrained('campus_students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('campus_courses')->onDelete('cascade');
            $table->foreignId('season_id')->constrained('campus_seasons')->onDelete('cascade');
            
            // Informació acadèmica
            $table->date('enrollment_date')->default(now());
            $table->enum('academic_status', [
                'enrolled',    // Matriculat
                'active',      // Actiu al curs
                'completed',   // Ha completat el curs
                'dropped',     // Ha abandonat
                'transferred', // Transferit
                'suspended'    // Suspès
            ])->default('enrolled');
            
            // Dates acadèmiques
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('completion_date')->nullable();
            
            // Avaluació
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->string('grade_letter')->nullable();
            $table->enum('grade_status', ['pending', 'graded', 'appealed', 'final'])->default('pending');
            
            // Assistència
            $table->enum('attendance_status', ['regular', 'irregular', 'excellent', 'poor'])->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();
            
            // Metadades
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Índexos únics per evitar duplicats
            $table->unique(['student_id', 'course_id', 'season_id'], 'unique_student_course_season');
            
            // Índexos per a consultes
            $table->index(['season_id', 'academic_status']);
            $table->index(['course_id', 'academic_status']);
            $table->index(['student_id', 'academic_status']);
            $table->index(['season_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_course_student');
    }
};
