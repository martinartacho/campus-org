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
            $table->text('iban')->nullable()->change();
        });

        Schema::table('campus_teacher_payments', function (Blueprint $table) {
            $table->text('iban')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_teachers', function (Blueprint $table) {
            $table->string('iban', 255)->nullable()->change();
        });

        Schema::table('campus_teacher_payments', function (Blueprint $table) {
            $table->string('iban', 255)->nullable()->change();
        });
    }
};
