<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('treasury_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('key');
            $table->text('value')->nullable();

            $table->timestamps();

            $table->unique(['teacher_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treasury_data');
    }
};
