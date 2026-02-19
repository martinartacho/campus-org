<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('campus_teacher_payments', function (Blueprint $table) {
        $table->id();

        $table->foreignId('teacher_id')->constrained('campus_teachers')->cascadeOnDelete();
        $table->foreignId('course_id')->constrained('campus_courses')->cascadeOnDelete();
        $table->foreignId('season_id')->constrained('campus_seasons')->cascadeOnDelete();

        $table->enum('payment_option', ['own_fee', 'ceded_fee', 'waived_fee'])->nullable();

        // snapshot dades financeres
        $table->string('first_name')->nullable();
        $table->string('last_name')->nullable();
        $table->string('fiscal_id')->nullable();// DNI / NIE → obligatori sempre
        $table->string('postal_code')->nullable();
        $table->string('city')->nullable();
        $table->string('fiscal_situation')->nullable();
        $table->string('invoice')->nullable();
        $table->text('observacions')->nullable();
        $table->string('address')->nullable();
           
        $table->string('iban')->nullable();
        $table->string('bank_titular')->nullable(); // bankHolder es el titular del compte
        $table->json('metadata')->nullable();
        $table->enum('needs_payment', ['own_fee', 'ceded_fee', 'waived_fee'])->default('own_fee');

        $table->timestamps();

        $table->unique(['teacher_id', 'course_id', 'season_id']);
   
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_teacher_payments');
    }
};

