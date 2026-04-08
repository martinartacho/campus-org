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
            // Afegir camp parent_id si no existeix (sense clau forànea per ara)
            if (!Schema::hasColumn('campus_courses', 'parent_id')) {
                $table->bigInteger('parent_id')->unsigned()->nullable()->after('code');
            }
                
            // Afegir camp sessions si no existeix
            if (!Schema::hasColumn('campus_courses', 'sessions')) {
                $table->integer('sessions')->nullable()->default(15)->after('hours');
            }
        });
        
        // Afegir la clau forànea per separat si la columna existeix
        if (Schema::hasColumn('campus_courses', 'parent_id')) {
            Schema::table('campus_courses', function (Blueprint $table) {
                $table->foreign('parent_id')
                    ->references('id')
                    ->on('campus_courses')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            // Eliminar primer la clau forànea si existeix
            if (Schema::hasColumn('campus_courses', 'parent_id')) {
                $table->dropForeign(['parent_id']);
            }
            
            // Després eliminar les columnes
            $columnsToDrop = [];
            if (Schema::hasColumn('campus_courses', 'parent_id')) {
                $columnsToDrop[] = 'parent_id';
            }
            if (Schema::hasColumn('campus_courses', 'sessions')) {
                $columnsToDrop[] = 'sessions';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
