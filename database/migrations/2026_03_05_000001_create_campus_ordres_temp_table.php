<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_ordres_temp', function (Blueprint $table) {
            $table->id();
            
            // Dades originals de WordPress
            $table->string('wp_first_name', 100);
            $table->string('wp_last_name', 100);
            $table->string('wp_email', 255);
            $table->string('wp_phone', 50)->nullable();
            $table->string('wp_item_name', 255);
            $table->string('wp_code', 50); // codi de l'ordre
            $table->string('wp_status', 50)->default('pending');
            $table->integer('wp_quantity')->default(1);
            $table->decimal('wp_price', 10, 2)->default(0.00);
            
            // Camps de processament
            $table->string('course_code', 50)->nullable(); // codi del curs trobat
            $table->unsignedBigInteger('course_id')->nullable(); // ID del curs trobat
            $table->enum('validation_status', ['pending', 'matched', 'manual', 'error'])->default('pending');
            $table->text('validation_notes')->nullable();
            
            // Metadades
            $table->json('metadata')->nullable();
            $table->timestamp('imported_at')->useCurrent();
            $table->timestamp('validated_at')->nullable();
            $table->unsignedBigInteger('validated_by')->nullable();
            
            // Índexs
            $table->index(['wp_code']);
            $table->index(['validation_status']);
            $table->index(['course_id']);
            $table->index(['wp_email']);
            
            // Foreign Keys
            $table->foreign('course_id')->references('id')->on('campus_courses')->nullOnDelete();
            $table->foreign('validated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_ordres_temp');
    }
};
