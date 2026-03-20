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
        Schema::create('dashboard_widget_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('widget_name'); // 'recent_registrations', 'courses_status', etc.
            $table->string('role_name'); // 'coordinacio', 'secretaria', 'gestio', etc.
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            
            // Índices únicos para evitar duplicados
            $table->unique(['widget_name', 'role_name'], 'widget_role_unique');
            $table->index('role_name');
            $table->index('widget_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_widget_permissions');
    }
};
