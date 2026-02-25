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
            // Change existing columns from JSON to TEXT
            $table->text('requirements')->nullable()->change();
            $table->text('objectives')->nullable()->change();
            
            // Add new columns
            $table->integer('sessions')->nullable()->after('hours');
            $table->bigInteger('space_id')->unsigned()->nullable()->after('location');
            $table->bigInteger('time_slot_id')->unsigned()->nullable()->after('space_id');
            $table->enum('status', ['draft', 'planning', 'in_progress', 'completed', 'closed'])
                  ->default('draft')
                  ->after('is_public');
            
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
            // Drop foreign keys first
            $table->dropForeign(['space_id']);
            $table->dropForeign(['time_slot_id']);
            
            // Drop new columns
            $table->dropColumn(['sessions', 'space_id', 'time_slot_id', 'status']);
            
            // Revert JSON columns
            $table->json('requirements')->nullable()->change();
            $table->json('objectives')->nullable()->change();
        });
    }
};
