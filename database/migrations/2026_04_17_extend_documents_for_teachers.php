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
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('campus_courses')->onDelete('cascade');
            $table->enum('document_type', ['material', 'tarea', 'evaluacion', 'recurso'])->nullable();
            $table->enum('student_visibility', ['all', 'course', 'private'])->default('course');
            $table->year('academic_year')->nullable();
            
            // Índices para optimización
            $table->index(['teacher_id', 'document_type']);
            $table->index(['course_id', 'student_visibility']);
            $table->index(['academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['course_id']);
            $table->dropColumn(['teacher_id', 'course_id', 'document_type', 'student_visibility', 'academic_year']);
        });
    }
};
