<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ej: "2025-26"
            $table->string('slug')->unique();
            $table->string('academic_year'); // Ej: "2025-2026"
            $table->date('registration_start');
            $table->date('registration_end');
            $table->date('season_start');
            $table->date('season_end');
            $table->enum('type', ['annual', 'semester', 'trimester', 'quarter']);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_current')->default(false);
            $table->json('periods')->nullable(); // Para múltiples periodos dentro de la temporada
            $table->timestamps();
            
            $table->index(['is_active', 'is_current']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_seasons');
    }
};