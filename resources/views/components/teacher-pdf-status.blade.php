@if($hasIban)
    @if($isPdfUpdated)
        <div class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-800 border border-green-300">
            <i class="bi bi-check-circle-fill mr-1"></i>
            <span class="text-xs font-medium">PDF</span>
        </div>
    @else
        <div class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 border border-yellow-300">
            <i class="bi bi-exclamation-triangle-fill mr-1"></i>
            <span class="text-xs font-medium">PDF</span>
        </div>
    @endif
@else
    <div class="inline-flex items-center px-2 py-1 rounded-full bg-red-100 text-red-800 border border-red-300">
        <i class="bi bi-x-circle-fill mr-1"></i>
        <span class="text-xs font-medium">PDF</span>
    </div>
@endif