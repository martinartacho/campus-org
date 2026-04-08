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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('document_categories')->onDelete('cascade');
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->string('file_type', 50);
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
