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
        Schema::table('campus_course_teacher', function (Blueprint $table) {
            $table->renameColumn('hours_assigned', 'sessions_assigned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_course_teacher', function (Blueprint $table) {
            $table->renameColumn('sessions_assigned', 'hours_assigned');
        });
    }
};
