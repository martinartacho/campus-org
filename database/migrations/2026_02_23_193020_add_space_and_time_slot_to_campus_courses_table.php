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
            $table->unsignedBigInteger('space_id')->nullable()->after('location');
            $table->unsignedBigInteger('time_slot_id')->nullable()->after('space_id');
            
            // Add foreign keys
            $table->foreign('space_id')->references('id')->on('campus_spaces')->onDelete('set null');
            $table->foreign('time_slot_id')->references('id')->on('campus_time_slots')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            $table->dropForeign(['space_id']);
            $table->dropForeign(['time_slot_id']);
            $table->dropColumn(['space_id', 'time_slot_id']);
        });
    }
};
