<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            // First, make the column nullable if it's not already
            $table->enum('level', ['none', 'beginner', 'intermediate', 'advanced', 'expert'])->nullable()->change();
        });
        
        // Update existing null values to 'none' as default
        DB::table('campus_courses')
            ->whereNull('level')
            ->update(['level' => 'none']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            // Revert to original enum
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable()->change();
        });
        
        // Set 'none' values back to null
        DB::table('campus_courses')
            ->where('level', 'none')
            ->update(['level' => null]);
    }
};
