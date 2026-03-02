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
        Schema::create('help_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('area'); // cursos, matricula, materiales, configuracion
            $table->string('context');
            $table->string('type')->nullable();
            $table->enum('status', ['draft', 'validated', 'obsolete'])->default('draft');
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('help_category_id')->nullable();
            $table->string('version')->default('1.0');
            $table->longText('content');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['area']);
            $table->index(['status']);
            $table->index(['order']);
            $table->index(['help_category_id']);
            $table->index(['created_by']);
            $table->index(['updated_by']);
            
            // Foreign keys
            $table->foreign('help_category_id')->references('id')->on('help_categories')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_articles');
    }
};
