<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('campus_seasons')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('campus_categories')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('credits')->default(0);
            $table->integer('hours')->default(0);
            $table->integer('max_students')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert']);
            $table->json('schedule')->nullable(); // Horarios en formato JSON
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location')->nullable();
            $table->string('format')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true); 
            $table->string('created_by')->nullable();
            $table->string('source')->nullable();

            $table->json('requirements')->nullable();
            $table->json('objectives')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['season_id', 'is_active']);
            $table->index(['is_public']);
            $table->index(['created_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_courses');
        $table->dropIndex(['created_by']);
        $table->dropColumn(['created_by', 'source']);
    }
};