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
        Schema::create('dashboard_quick_action_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
            $table->string('action_name');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            
            // Índices únicos
            $table->unique(['role_name', 'action_name']);
            $table->index('role_name');
            $table->index('action_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_quick_action_permissions');
    }
};
