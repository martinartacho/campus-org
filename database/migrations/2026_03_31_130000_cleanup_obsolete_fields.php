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
        Schema::table('campus_teachers', function (Blueprint $table) {
            // Eliminar només els camps que existeixen
            $table->dropColumn([
                'waived_confirmation',  // Substituït per fiscal_responsibility
                'own_confirmation',     // Substituït per data_consent
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_teachers', function (Blueprint $table) {
            $table->boolean('waived_confirmation')->default(false);
            $table->boolean('own_confirmation')->default(false);
        });
    }
};
