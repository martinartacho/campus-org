<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('campus_students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('campus_courses')->cascadeOnDelete();
            $table->string('registration_code')->unique();
            $table->date('registration_date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'failed'])->default('pending');
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'cancelled'])->default('pending');
            $table->date('payment_due_date')->nullable();
            $table->json('payment_history')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->enum('attendance_status', ['regular', 'irregular', 'absent'])->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'course_id']);
            $table->index(['status']);
            $table->index(['payment_status']);
            $table->unique(['student_id', 'course_id']); // Un estudiante solo puede matricularse una vez por curso
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_registrations');
    }
};