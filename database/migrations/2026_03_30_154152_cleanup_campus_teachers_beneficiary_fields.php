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
            // Eliminar tots els camps de beneficiari
            $table->dropColumn('beneficiary_iban');
            $table->dropColumn('beneficiary_titular');
            $table->dropColumn('beneficiary_fiscal_situation');
            $table->dropColumn('beneficiary_invoice');
            $table->dropColumn('beneficiary_city');
            $table->dropColumn('beneficiary_postal_code');
            $table->dropColumn('beneficiary_name');
            $table->dropColumn('beneficiary_nif');
            $table->dropColumn('beneficiary_contact_person');
            $table->dropColumn('beneficiary_contact_phone');
            $table->dropColumn('beneficiary_observations');
            $table->dropColumn('beneficiary_first_name');
            $table->dropColumn('beneficiary_last_name');
            $table->dropColumn('beneficiary_email');
            $table->dropColumn('ceded_confirmation');
            $table->dropColumn('needs_payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_teachers', function (Blueprint $table) {
            // Restaurar tots els camps de beneficiari
            $table->text('beneficiary_iban')->nullable()->after('payment_pdf_path');
            $table->text('beneficiary_titular')->nullable()->after('beneficiary_iban');
            $table->enum('beneficiary_fiscal_situation', ['autonom', 'employee', 'pensioner', 'other'])->nullable()->after('beneficiary_titular');
            $table->boolean('beneficiary_invoice')->default(false)->after('beneficiary_fiscal_situation');
            $table->string('beneficiary_city', 255)->nullable()->after('beneficiary_invoice');
            $table->string('beneficiary_postal_code', 255)->nullable()->after('beneficiary_city');
            $table->string('beneficiary_name', 255)->nullable()->after('beneficiary_postal_code');
            $table->string('beneficiary_nif', 20)->nullable()->after('beneficiary_name');
            $table->string('beneficiary_contact_person', 255)->nullable()->after('beneficiary_nif');
            $table->string('beneficiary_contact_phone', 20)->nullable()->after('beneficiary_contact_person');
            $table->text('beneficiary_observations')->nullable()->after('beneficiary_contact_phone');
            $table->string('beneficiary_first_name', 255)->nullable()->after('beneficiary_observations');
            $table->string('beneficiary_last_name', 255)->nullable()->after('beneficiary_first_name');
            $table->string('beneficiary_email', 255)->nullable()->after('beneficiary_last_name');
            $table->boolean('ceded_confirmation')->default(false)->after('beneficiary_email');
            $table->enum('needs_payment', ['own_fee', 'ceded_fee', 'waived_fee'])->default('waived_fee')->after('ceded_confirmation');
        });
    }
};
