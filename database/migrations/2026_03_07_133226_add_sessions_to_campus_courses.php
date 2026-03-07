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
        Schema::table('campus_courses', function (Blueprint $table) {
            // Afegir camp sessions
            $table->integer('sessions')->nullable()->default(15)->after('hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            $table->dropColumn('sessions');
        });
    }
};
