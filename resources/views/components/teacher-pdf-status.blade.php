@php
    // Lògica millorada: considerar el tipus de pagament
    $needsIban = !in_array($teacher->payment_type ?? 'ceded', ['waived', 'own']);
    $hasValidIban = !empty($teacher->iban) || !$needsIban;
@endphp

<!-- @if($hasValidIban)  {{-- Té IBAN --}}
    @if($isPdfUpdated)
        <div class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-800 border border-green-300" title="PDF correcte i actualitzat">
            <i class="bi bi-check-circle-fill mr-1"></i>
            <span class="text-xs font-medium">1. PDF </span>
        </div>
    @endif
@else -->
    @if($teacher->payment_type === 'waived')
        <div class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-300" title="PDF vàlid - pagament exent">
            <i class="bi bi-info-circle-fill mr-1"></i>
            <span class="text-xs font-medium">3. Renunciar al cobrament</span>
        </div>
    @endif
    @if($teacher->payment_type === 'own')
        <div class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-300" title="PDF vàlid - Cobrament propi">
            <i class="bi bi-info-circle-fill mr-1"></i>
            <span class="text-xs font-medium">3. Cobrament propi</span>
        </div>
    @endif
    @if($teacher->payment_type === 'ceded')
        <div class="inline-flex items-center px-2 py-1 rounded-full bg-red-100 text-red-800 border border-red-300" title="PDF correcte però sense IBAN">
            <i class="bi bi-exclamation-triangle-fill mr-1"></i>
            <span class="text-xs font-medium">5. PDF Correcte sense IBAN</span>
        </div>
    @endif

<!-- @endif -->