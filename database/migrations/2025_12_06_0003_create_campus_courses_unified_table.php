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
            
            // Course identification
            $table->string('code')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Basic relations
            $table->foreignId('season_id')->constrained('campus_seasons')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('campus_categories')->nullOnDelete();
            $table->foreignId('parent_id')->unsigned()->nullable(); // For course hierarchy
            
            // Course details
            $table->integer('hours')->default(0);
            $table->integer('sessions')->nullable(); // Number of sessions
            $table->integer('max_students')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('level', ['none', 'beginner', 'intermediate', 'advanced', 'expert'])->nullable();
            
            // Schedule and location
            $table->json('schedule')->nullable(); // Horarios en formato JSON
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location')->nullable();
            $table->bigInteger('space_id')->unsigned()->nullable();
            $table->bigInteger('time_slot_id')->unsigned()->nullable();
            $table->string('format')->nullable();
            
            // Status and visibility
            $table->enum('status', ['draft', 'planning', 'in_progress', 'completed', 'closed'])
                  ->default('draft');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            
            // Content and metadata
            $table->text('requirements')->nullable(); // TEXT from start (not JSON)
            $table->text('objectives')->nullable(); // TEXT from start (not JSON)
            $table->json('metadata')->nullable();
            
            // Control fields
            $table->string('created_by')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['season_id', 'is_active']);
            $table->index(['is_public']);
            $table->index(['created_by']);
            $table->index(['category_id', 'is_active']);
            $table->index(['status']);
            
            // Foreign keys (added separately to avoid issues)
        });
        
        // Foreign keys will be added in separate migrations to avoid dependency issues
        // parent_id -> campus_courses (self-reference)
        // space_id -> campus_spaces (created 2026_02_20)
        // time_slot_id -> campus_time_slots (created 2026_02_20)
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_courses');
    }
};
