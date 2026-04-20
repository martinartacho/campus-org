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
        Schema::table('campus_seasons', function (Blueprint $table) {
            // Ampliar tipos de períodos
            $table->enum('type', [
                'annual',      // Año académico completo
                'semester',    // Semestre (6 meses)
                'trimester',   // Trimestre (3 meses)
                'quarter',     // Cuatrimestre (4 meses)
                'bimensual',   // Bimensual (2 meses)
                'monthly',     // Mensual (1 mes)
                'custom'       // Período personalizado
            ])->change();
            
            // Índice para prevenir duplicados iguales
            $table->index(['parent_id', 'type'], 'idx_seasons_parent_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_seasons', function (Blueprint $table) {
            // Revertir a tipos originales
            $table->enum('type', [
                'annual',
                'semester', 
                'trimester', 
                'quarter'
            ])->change();
            
            // Eliminar índice agregado
            $table->dropIndex('idx_seasons_parent_type_unique');
        });
    }
};
