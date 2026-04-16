{{-- resources/views/components/dashboard/widgets/student_documents.blade.php --}}
@php
    $user = auth()->user();
    
    // Obtener documentos disponibles para el estudiante
    $availableDocuments = \App\Models\Document::with(['course', 'teacher'])
        ->where(function($q) use ($user) {
            // Documentos de profesor visibles para este estudiante
            $q->whereHas('teacher', function($subQuery) {
                $subQuery->whereNotNull('id');
            })->where(function($visibilityQuery) use ($user) {
                // Documentos públicos para estudiantes
                $visibilityQuery->where('student_visibility', 'all')
                    // Documentos del curso del estudiante
                    ->orWhere(function($courseQuery) use ($user) {
                        $courseQuery->where('student_visibility', 'course')
                            ->whereHas('course.students', function($studentQuery) use ($user) {
                                $studentQuery->where('user_id', $user->id)
                                    ->where('academic_status', 'active');
                            });
                    });
            });
        })
        ->active();
    
    $stats = [
        'total_documents' => $availableDocuments->count(),
        'course_documents' => $availableDocuments->whereNotNull('course_id')->count(),
        'public_documents' => $availableDocuments->where('student_visibility', 'all')->count(),
        'recent_downloads' => \App\Models\DocumentDownload::where('user_id', $user->id)
            ->where('downloaded_at', '>=', now()->subDays(7))
            ->count(),
    ];
    
    // Documentos recientes para mostrar
    $recentDocuments = $availableDocuments
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();
@endphp

<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-journal-text text-blue-600 me-2"></i>
        Documents Disponibles
    </h3>
    
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_documents'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Total</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['course_documents'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Dels Meus Cursos</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['public_documents'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Públics</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $stats['recent_downloads'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Descàrregues (7d)</div>
        </div>
    </div>
    
    @if($recentDocuments->count() > 0)
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Documents Recents</h4>
            <div class="space-y-2">
                @foreach($recentDocuments as $doc)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded hover:bg-gray-100 transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center">
                                <i class="{{ $doc->file_icon }} text-gray-400 mr-2"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->title }}</p>
                                    @if($doc->course)
                                        <p class="text-xs text-gray-500">{{ $doc->course->title }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('student.documents.download', $doc) }}" 
                           class="ml-2 p-1 text-blue-600 hover:text-blue-800"
                           title="Descarregar">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <div class="mt-4">
        <x-campus-button href="{{ route('student.documents.index') }}" variant="primary" size="sm">
            <i class="bi bi-folder2-open me-2"></i>
            Veure Tots els Documents
        </x-campus-button>
    </div>
</div>
