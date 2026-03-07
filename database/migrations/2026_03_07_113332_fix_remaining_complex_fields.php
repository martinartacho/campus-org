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
            // Eliminar camps que encara poden existir
            if (Schema::hasColumn('campus_courses', 'is_base_course')) {
                $table->dropColumn('is_base_course');
            }
            if (Schema::hasColumn('campus_courses', 'parent_base_id')) {
                $table->dropColumn('parent_base_id');
            }
            if (Schema::hasColumn('campus_courses', 'base_code')) {
                $table->dropColumn('base_code');
            }
            if (Schema::hasColumn('campus_courses', 'instance_code')) {
                $table->dropColumn('instance_code');
            }
            if (Schema::hasColumn('campus_courses', 'credits')) {
                $table->dropColumn('credits');
            }
            if (Schema::hasColumn('campus_courses', 'sessions')) {
                $table->dropColumn('sessions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            // Afegir camps si no existeixen
            if (!Schema::hasColumn('campus_courses', 'is_base_course')) {
                $table->boolean('is_base_course')->default(true);
            }
            if (!Schema::hasColumn('campus_courses', 'parent_base_id')) {
                $table->bigInteger('parent_base_id')->nullable();
            }
            if (!Schema::hasColumn('campus_courses', 'base_code')) {
                $table->string('base_code', 50)->nullable()->unique();
            }
            if (!Schema::hasColumn('campus_courses', 'instance_code')) {
                $table->string('instance_code', 100)->nullable()->unique();
            }
            if (!Schema::hasColumn('campus_courses', 'credits')) {
                $table->integer('credits')->default(0);
            }
            if (!Schema::hasColumn('campus_courses', 'sessions')) {
                $table->integer('sessions')->default(0);
            }
        });
    }
};
