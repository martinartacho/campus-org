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
            // Tipus de cobrament
            $table->enum('payment_type', ['waived', 'own', 'ceded'])->nullable()
                  ->comment('waived: no cobren, own: cobren ells, ceded: cedeixen cobrament');
            
            // Dades del beneficiari (per ceded)
            $table->string('beneficiary_dni')->nullable()
                  ->comment('DNI/NIF del beneficiari (només per ceded)');
            $table->string('beneficiary_iban')->nullable()
                  ->comment('IBAN del beneficiari (només per ceded)');
            $table->string('beneficiary_titular')->nullable()
                  ->comment('Titular del compte beneficiari (només per ceded)');
            $table->enum('beneficiary_fiscal_situation', ['autonom', 'employee', 'pensioner', 'other'])->nullable()
                  ->comment('Situació fiscal del beneficiari');
            $table->boolean('beneficiary_invoice')->default(false)
                  ->comment('El beneficiari presentarà factura');
            $table->string('beneficiary_city')->nullable()
                  ->comment('Ciutat del beneficiari (només per ceded)');
            $table->string('beneficiary_postal_code')->nullable()
                  ->comment('Codi postal del beneficiari (només per ceded)');
            
            // Confirmacions
            $table->boolean('waived_confirmation')->default(false)
                  ->comment('Confirmació de no cobrament');
            $table->boolean('own_confirmation')->default(false)
                  ->comment('Confirmació de cobrament propi');
            $table->boolean('ceded_confirmation')->default(false)
                  ->comment('Confirmació de cobrament cedit');
            
            // Estat del pagament
            $table->enum('payment_status', ['pending', 'confirmed', 'processed'])->default('pending')
                  ->comment('Estat del procés de pagament');
            $table->timestamp('payment_confirmed_at')->nullable()
                  ->comment('Data de confirmació de dades de pagament');
            $table->string('payment_pdf_path')->nullable()
                  ->comment('Ruta al PDF de confirmació de pagament');
            
            // Indexos per rendiment
            $table->index('payment_type');
            $table->index('payment_status');
            $table->index(['payment_type', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_teachers', function (Blueprint $table) {
            $table->dropIndex(['payment_type', 'payment_status']);
            $table->dropIndex('payment_status');
            $table->dropIndex('payment_type');
            
            $table->dropColumn([
                'payment_type',
                'beneficiary_dni',
                'beneficiary_iban', 
                'beneficiary_titular',
                'beneficiary_fiscal_situation',
                'beneficiary_invoice',
                'beneficiary_city',
                'beneficiary_postal_code',
                'waived_confirmation',
                'own_confirmation',
                'ceded_confirmation',
                'payment_status',
                'payment_confirmed_at',
                'payment_pdf_path'
            ]);
        });
    }
};
