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
            // Eliminar índex que utilitza semester_number
            $table->dropIndex(['parent_id', 'semester_number']);
            
            // Eliminar columna semester_number (obsoleta)
            $table->dropColumn('semester_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_seasons', function (Blueprint $table) {
            // Afegir columna semester_number
            $table->tinyInteger('semester_number')->nullable()->after('type');
            
            // Recrear índex
            $table->index(['parent_id', 'semester_number']);
        });
    }
};
