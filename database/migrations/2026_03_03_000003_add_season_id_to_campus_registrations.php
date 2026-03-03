<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campus_registrations', function (Blueprint $table) {
            // Afegir season_id per mantenir coherència amb altres taules
            $table->foreignId('season_id')->nullable()->after('course_id')
                  ->constrained('campus_seasons')->onDelete('cascade');
            
            // Índexos per a consultes
            $table->index(['season_id', 'status']);
            $table->index(['season_id', 'payment_status']);
            $table->index(['student_id', 'season_id']);
        });
    }

    public function down(): void
    {
        Schema::table('campus_registrations', function (Blueprint $table) {
            $table->dropForeign(['season_id']);
            $table->dropIndex(['season_id', 'status']);
            $table->dropIndex(['season_id', 'payment_status']);
            $table->dropIndex(['student_id', 'season_id']);
            $table->dropColumn('season_id');
        });
    }
};
