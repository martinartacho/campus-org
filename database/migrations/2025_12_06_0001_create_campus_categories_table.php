<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('blue');
            $table->string('icon')->default('tag');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('campus_categories')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'is_featured']);
            $table->index(['parent_id', 'order']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_categories');
    }
};