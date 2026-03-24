<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Crypt;

$teacher = App\Models\CampusTeacher::find(48);

// Extreure l'IBAN correcte
$raw = $teacher->getRawOriginal('iban');
$decrypted = Crypt::decrypt($raw);

echo "Raw: " . $raw . "\n";
echo "Decrypted: " . $decrypted . "\n";

// Si està serialitzat, extreure el valor real
if (strpos($decrypted, 's:') === 0) {
    // Extreure el valor de la cadena serialitzada
    preg_match('/s:\d+:"([^"]+)"/', $decrypted, $matches);
    if (isset($matches[1])) {
        $iban = $matches[1];
        echo "IBAN extret: " . $iban . "\n";
        
        // Guardar correctament sense serialitzar
        $teacher->iban = $iban;
        $teacher->save();
        echo "IBAN guardat correctament\n";
    }
} else {
    echo "IBAN ja està correcte: " . $decrypted . "\n";
}

// Verificar
$teacher = App\Models\CampusTeacher::find(48);
echo "IBAN final: " . $teacher->iban . "\n";
echo "Formatted IBAN: " . $teacher->formatted_iban . "\n";
