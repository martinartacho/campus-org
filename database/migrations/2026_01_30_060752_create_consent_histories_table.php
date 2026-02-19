<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consent_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnDelete();
            
                
            // excepcionly for teacher 
            $table->foreignId('delegated_by_user_id')
                ->nullable()
                ->nullOnDelete();

            $table->text('delegated_reason')->nullable();
            

            $table->string('season'); // ex: 2025-2026
            $table->string('document_path')->nullable(); // PDF de consentiment
            $table->string('payment_document_path')->nullable(); // PDF de dades bancariese
            
            
            $table->timestamp('accepted_at');
            $table->string('checksum'); // hash del PDF
            
            $table->timestamps();

            $table->unique(['teacher_id', 'season']);
        });
    }


    public function down(): void
    {
        Schema::table('consent_histories', function (Blueprint $table) {
            $table->dropForeign(['delegated_by_user_id']);
            $table->dropColumn(['delegated_by_user_id', 'delegated_reason']);
        });
    }
};
