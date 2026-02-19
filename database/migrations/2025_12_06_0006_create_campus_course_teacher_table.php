<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_course_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('campus_courses')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('campus_teachers')->cascadeOnDelete();
            $table->string('role')->default('teacher'); // profesor, coordinator, tutor, etc.
            $table->decimal('hours_assigned', 5, 2)->default(0);
            $table->timestamp('assigned_at')->nullable(); 
            $table->date('finished_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['course_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_course_teacher');
    }
};