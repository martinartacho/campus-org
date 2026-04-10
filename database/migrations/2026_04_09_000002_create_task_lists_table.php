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
        Schema::create('task_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_id');
            $table->string('name');
            $table->integer('order')->default(0);
            $table->string('color')->default('#6B7280')->comment('Color hex per la columna');
            $table->boolean('is_default')->default(false)->comment('Per auto-creació en taules noves');
            $table->timestamps();

            // Índexs
            $table->index('board_id');
            $table->index(['board_id', 'order']);

            // Foreign keys
            $table->foreign('board_id')->references('id')->on('task_boards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_lists');
    }
};
