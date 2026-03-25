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
        Schema::create('release_notes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('version'); // v1.2.3
            $table->enum('type', ['major', 'minor', 'patch'])->default('minor');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->json('features')->nullable(); // Array de novetats
            $table->json('improvements')->nullable(); // Array de millores
            $table->json('fixes')->nullable(); // Array de correccions
            $table->json('breaking_changes')->nullable(); // Array de canvis disruptius
            $table->json('affected_modules')->nullable(); // Mòduls afectats
            $table->json('target_audience')->nullable(); // Usuaris afectats
            $table->json('commits')->nullable(); // Commits inclosos
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['version']);
            $table->index(['status']);
            $table->index(['type']);
            $table->index(['published_at']);
            $table->index(['created_by']);
            $table->index(['published_by']);
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('published_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_notes');
    }
};
