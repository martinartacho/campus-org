<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('event_question_templates', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->enum('type', ['single', 'multiple', 'text']);
            $table->json('options')->nullable();
            $table->boolean('required')->default(false);
            $table->boolean('is_template')->default(false);
            $table->string('template_name')->nullable();
            $table->text('template_description')->nullable();
            $table->timestamps();
            
            // Índices para búsquedas eficientes
            $table->index('is_template');
            $table->index('template_name');

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_question_templates');
    }
};
