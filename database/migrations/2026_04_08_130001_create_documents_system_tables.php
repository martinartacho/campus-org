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
        // Create document_categories first
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 7)->nullable(); // Hex color
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });

        // Create documents table with correct file_type size from start
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('document_categories')->onDelete('cascade');
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->string('file_type', 255); // Fixed size from start
            $table->integer('file_size');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->json('access_roles')->nullable(); // Roles que pueden acceder
            $table->boolean('is_public')->default(false);
            $table->date('document_date')->nullable(); // Fecha del documento
            $table->string('reference', 100)->nullable(); // Referencia del documento
            $table->text('tags')->nullable(); // Etiquetas para búsqueda
            $table->boolean('is_active')->default(true);
            $table->integer('download_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->index(['uploaded_by']);
            $table->index(['document_date']);
            $table->fullText(['title', 'description', 'tags']);
        });

        // Create document_downloads last (depends on documents)
        Schema::create('document_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('downloaded_at');
            
            $table->index(['document_id', 'user_id']);
            $table->index(['downloaded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_downloads');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_categories');
    }
};
