<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('teacher_code')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('dni')->nullable();
            $table->string('email')->nullable(); // Email específico del profesor
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('observacions')->nullable();
            
            // Banking data (original)
            $table->string('iban')->nullable();
            $table->string('bank_titular')->nullable(); //$bankHolder
            $table->string('fiscal_id')->nullable();
            $table->string('fiscal_situation')->nullable();
            $table->enum('needs_payment', ['own_fee', 'ceded_fee', 'waived_fee'])->default('own_fee');
            $table->string('invoice')->nullable();
            
            // Payment fields (added from 2026_03_23 migration)
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
            
            // Academic data
            $table->string('degree')->nullable();
            $table->string('specialization')->nullable();
            $table->string('title')->nullable(); // Dr., Prof., etc.
            $table->json('areas')->nullable(); // Àreas de especializació
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->date('hiring_date'); // Data de contratació
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status']);
            $table->index(['teacher_code']);
            $table->index('payment_type');
            $table->index('payment_status');
            $table->index(['payment_type', 'payment_status']);
            $table->unique(['user_id']); // Un usuari nomes pot tenir  un perfil de professor
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_teachers');
    }
};
