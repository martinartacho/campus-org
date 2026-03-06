<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            // Camps per traçabilitat
            $table->string('base_code', 50)->nullable()->unique()->after('code');
            $table->string('instance_code', 100)->nullable()->unique()->after('base_code');
            $table->boolean('is_base_course')->default(true)->after('instance_code');
            $table->bigInteger('parent_base_id')->nullable()->after('is_base_course');
            
            // Indexos per rendiment
            $table->index('base_code');
            $table->index('instance_code');
            $table->index('parent_base_id');
        });
    }

    public function down()
    {
        Schema::table('campus_courses', function (Blueprint $table) {
            $table->dropIndex(['base_code']);
            $table->dropIndex(['instance_code']);
            $table->dropIndex(['parent_base_id']);
            $table->dropColumn(['base_code', 'instance_code', 'is_base_course', 'parent_base_id']);
        });
    }
};
