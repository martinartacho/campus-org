<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

// Netejar primer
DB::table('campus_teachers')->where('id', 1)->update([
    'iban' => null,
    'bank_titular' => null,
    'fiscal_id' => null
]);

echo "Dades netejades\n";

// Ara provar de xifrar manualment
$teacher = DB::table('campus_teachers')->where('id', 1)->first();

if ($teacher) {
    $iban = 'ES56 2100 1234 5678 9012 3456';
    $titular = 'Dr. Joan Prat i Soler';
    $fiscalId = '12345678Z';
    
    try {
        $encryptedIban = Crypt::encrypt($iban);
        $encryptedTitular = Crypt::encrypt($titular);
        $encryptedFiscalId = Crypt::encrypt($fiscalId);
        
        echo "Xifrant...\n";
        echo "IBAN original: $iban\n";
        echo "IBAN xifrat: $encryptedIban\n\n";
        
        echo "Titular original: $titular\n";
        echo "Titular xifrat: $encryptedTitular\n\n";
        
        echo "Fiscal ID original: $fiscalId\n";
        echo "Fiscal ID xifrat: $encryptedFiscalId\n\n";
        
        // Actualitzar a la BD
        DB::table('campus_teachers')->where('id', 1)->update([
            'iban' => $encryptedIban,
            'bank_titular' => $encryptedTitular,
            'fiscal_id' => $encryptedFiscalId
        ]);
        
        echo "✅ Dades xifrades i guardades correctament!\n";
        
        // Provar desxifrat
        $check = DB::table('campus_teachers')->where('id', 1)->first();
        echo "Comprovació desxifrat:\n";
        echo "IBAN desxifrat: " . $check->iban . "\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Professor no trobat\n";
}
