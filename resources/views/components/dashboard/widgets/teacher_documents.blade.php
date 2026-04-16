{{-- resources/views/components/dashboard/widgets/teacher_documents.blade.php --}}
@php
    $user = auth()->user();
    $stats = [
        'total_documents' => \App\Models\Document::where('teacher_id', $user->id)->active()->count(),
        'course_documents' => \App\Models\Document::where('teacher_id', $user->id)->whereNotNull('course_id')->active()->count(),
        'recent_downloads' => \App\Models\DocumentDownload::whereHas('document', function($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })->where('downloaded_at', '>=', now()->subDays(7))->count(),
    ];
    
    $recentCount = \App\Models\Document::where('teacher_id', $user->id)
        ->where('created_at', '>=', now()->subDays(7))
        ->active()
        ->count();
@endphp

<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-journal-text text-green-600 me-2"></i>
        Meus Documents
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_documents'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Total</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['course_documents'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Per Curs</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['recent_downloads'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Descàrregues (7d)</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $recentCount }}</div>
            <div class="text-sm text-gray-600">Nous (7d)</div>
        </div>
    </div>
    
    <div class="mt-4">
        <x-campus-button href="{{ route('teacher.documents.index') }}" variant="primary" size="sm">
            <i class="bi bi-folder2-open me-2"></i>
            Gestionar Documents
        </x-campus-button>
    </div>
</div>
