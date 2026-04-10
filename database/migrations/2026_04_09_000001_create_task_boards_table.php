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
        Schema::create('task_boards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['course', 'team', 'global', 'department'])->default('team');
            $table->unsignedBigInteger('entity_id')->nullable()->comment('FK a cursos o departaments');
            $table->unsignedBigInteger('created_by');
            $table->enum('visibility', ['public', 'team', 'private'])->default('team');
            $table->timestamps();
            $table->softDeletes();

            // Índexs
            $table->index(['type', 'entity_id']);
            $table->index('created_by');
            $table->index('visibility');

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_boards');
    }
};
