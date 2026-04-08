<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            // Add foreign keys after spaces and time_slots exist
            $table->foreign('parent_id')
                ->references('id')
                ->on('campus_courses')
                ->nullOnDelete();
            $table->foreign('space_id')->references('id')->on('campus_spaces')->onDelete('set null');
            $table->foreign('time_slot_id')->references('id')->on('campus_time_slots')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['space_id']);
            $table->dropForeign(['time_slot_id']);
        });
    }
};
