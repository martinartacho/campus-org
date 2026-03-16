<?php
// Contenido corregido para la sección bancaria
$correct_content = '
        // Solo mostrar sección bancaria si no es renuncia al cobro
        if ($payment->payment_option !== \'waived_fee\') {
            $html .= \'
        <div class="section">
            <h2>4. DADES FISCALS I BANCÀRIES</h2>
            <div class="info-row">
                <span class="label">Identificació fiscal:</span>
                <span class="value">\' . htmlspecialchars($payment->fiscal_id ?? \'N/A\') . \'</span>
            </div>
            <div class="info-row">
                <span class="label">IBAN:</span>
                <span class="value">\' . htmlspecialchars($teacher->masked_iban ?? \'N/A\') . \'</span>
            </div>
            <div class="info-row">
                <span class="label">Titular del compte:</span>
                <span class="value">\' . htmlspecialchars($payment->bank_titular ?? \'N/A\') . \'</span>
            </div>
        </div>\';
        }';

echo $correct_content;
