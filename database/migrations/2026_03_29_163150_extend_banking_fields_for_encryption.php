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
            $table->text('bank_titular')->nullable()->change();
            $table->text('fiscal_id')->nullable()->change();
            $table->text('beneficiary_iban')->nullable()->change();
            $table->text('beneficiary_titular')->nullable()->change();
            $table->text('beneficiary_dni')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_teachers', function (Blueprint $table) {
            $table->string('iban', 255)->nullable()->change();
            $table->string('bank_titular', 255)->nullable()->change();
            $table->string('fiscal_id', 255)->nullable()->change();
            $table->string('beneficiary_iban', 255)->nullable()->change();
            $table->string('beneficiary_titular', 255)->nullable()->change();
            $table->string('beneficiary_dni', 255)->nullable()->change();
        });
    }
};
