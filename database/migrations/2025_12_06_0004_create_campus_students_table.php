<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('student_code')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('dni')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable(); // Campo específico para estudiante
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated', 'suspended', 'on_leave'])->default('active');
            $table->date('enrollment_date');
            $table->json('academic_record')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['student_code']);
            $table->unique(['user_id']); // Un usuario solo puede tener un perfil de estudiante
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_students');
    }
};