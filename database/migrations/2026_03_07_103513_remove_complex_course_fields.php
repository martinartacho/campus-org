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
        Schema::table('campus_courses', function (Blueprint $table) {
            // Eliminar camps complexos
            $table->dropColumn([
                'base_code',
                'instance_code', 
                'is_base_course',
                'parent_base_id',
                'credits',
                'sessions'
            ]);
            
            // Modificar camp level per ser nullable
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            // Afegir camps complexos
            $table->string('base_code', 50)->nullable()->unique()->after('code');
            $table->string('instance_code', 100)->nullable()->unique()->after('base_code');
            $table->boolean('is_base_course')->default(true)->after('instance_code');
            $table->bigInteger('parent_base_id')->nullable()->after('is_base_course');
            $table->integer('credits')->default(0)->after('description');
            $table->integer('sessions')->default(0)->after('credits');
            
            // Restaurar level com no nullable
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable(false)->change();
        });
    }
};
