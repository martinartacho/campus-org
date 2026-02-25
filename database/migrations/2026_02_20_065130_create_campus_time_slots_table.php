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
        Schema::create('campus_time_slots', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('day_of_week'); // 1-5: dl-dv
            $table->string('code'); // M11, T16, T18
            $table->time('start_time'); // 11:00:00, 16:00:00, 18:00:00
            $table->time('end_time'); // 12:30:00, 17:30:00, 19:30:00
            $table->string('description'); // Matí 11-12:30, Tarda 16-17:30, Tarda 18-19:30
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['day_of_week', 'code']);
            $table->index(['day_of_week', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campus_time_slots');
    }
};
