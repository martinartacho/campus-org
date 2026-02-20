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
        Schema::create('campus_spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Sala d'actes, Aula mitjana 1, etc.
            $table->string('code')->unique(); // SA, AM1, AP1, SP, EXT
            $table->integer('capacity'); // 50, 25, 10
            $table->enum('type', ['sala_actes', 'mitjana', 'petita', 'polivalent', 'extern']);
            $table->text('description')->nullable();
            $table->json('equipment')->nullable(); // ["projector", "tv", "audio", "ordinadors"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index(['capacity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campus_spaces');
    }
};
