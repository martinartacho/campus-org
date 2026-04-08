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
        Schema::table('campus_teachers', function (Blueprint $table) {
            // Canviar tinyint a varchar perquè funcionin com invoice
            $table->string('data_consent')->default('0')->change();
            $table->string('fiscal_responsibility')->default('0')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_teachers', function (Blueprint $table) {
            $table->boolean('data_consent')->default(false)->change();
            $table->boolean('fiscal_responsibility')->default(false)->change();
        });
    }
};
