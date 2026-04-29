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
        Schema::create('campus_non_lective_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('description')->nullable();
            $table->string('type')->default('general'); // general, holiday, exam, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('date');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campus_non_lective_days');
    }
};
