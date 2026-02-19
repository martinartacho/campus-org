<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('teacher_code')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('dni')->nullable();
            $table->string('email')->nullable(); // Email específico del profesor
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('observacions')->nullable();
            
            $table->string('iban')->nullable();
            $table->string('bank_titular')->nullable(); //$bankHolder
            $table->string('fiscal_id')->nullable();
            $table->string('fiscal_situation')->nullable();
            $table->enum('needs_payment', ['own_fee', 'ceded_fee', 'waived_fee'])->default('own_fee');
            $table->string('invoice')->nullable();
            
            $table->string('degree')->nullable();
            $table->string('specialization')->nullable();
            $table->string('title')->nullable(); // Dr., Prof., etc.
            $table->json('areas')->nullable(); // Àreas de especializació
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->date('hiring_date'); // Data de contratació
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['teacher_code']);
            $table->unique(['user_id']); // Un usuari nomes pot tenir  un perfil de professor
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_teachers');
    }
};