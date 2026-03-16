<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo " Iniciando encriptación de IBANs...\n";
        
        // Obtener IBANs directamente de la base de datos
        $teachers = DB::table('campus_teachers')->whereNotNull('iban')->get();
        
        foreach ($teachers as $teacher) {
            echo "Procesando profesor ID: {$teacher->id}\n";
            echo "IBAN actual: " . substr($teacher->iban, 0, 20) . "...\n";
            
            try {
                // Encriptar el IBAN
                $encryptedIban = encrypt($teacher->iban);
                echo "IBAN encriptado: " . substr($encryptedIban, 0, 50) . "...\n";
                
                // Actualizar directamente en la base de datos
                DB::table('campus_teachers')
                    ->where('id', $teacher->id)
                    ->update(['iban' => $encryptedIban]);
                
                echo " Profesor {$teacher->id} actualizado\n\n";
            } catch (\Exception $e) {
                echo " Error en profesor {$teacher->id}: " . $e->getMessage() . "\n\n";
            }
        }
        
        echo " Encriptación de IBANs completada\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Aquí podríamos desencriptar si fuera necesario
        echo " Esta migración no tiene rollback automático\n";
    }
};
