<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campus_seasons', function (Blueprint $table) {
            // Afegir jerarquia
            $table->foreignId('parent_id')->nullable()->after('id')
                  ->constrained('campus_seasons')->onDelete('cascade');
            
            // Afegir número de semestre/quadrimestre per ordenar
            $table->tinyInteger('semester_number')->nullable()->after('type');
            
            // Índexos per a consultes jeràrquiques
            $table->index(['parent_id', 'type']);
            $table->index(['parent_id', 'semester_number']);
        });
    }

    public function down(): void
    {
        Schema::table('campus_seasons', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id', 'type']);
            $table->dropIndex(['parent_id', 'semester_number']);
            $table->dropColumn(['parent_id', 'semester_number']);
        });
    }
};
