<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get accessible categories for the user
        $categories = DocumentCategory::with(['children' => function($query) {
            $query->active()->orderBy('sort_order');
        }])
        ->root()
        ->active()
        ->where(function($query) use ($user) {
            $query->whereNull('access_roles')
                  ->orWhereJsonContains('access_roles', $user->roles->pluck('name'));
        })
        ->orderBy('sort_order')
        ->get();

        // Build query for documents
        $query = Document::with(['category', 'uploader'])
            ->active()
            ->accessibleBy($user);

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('year')) {
            $query->byYear($request->year);
        }

        // Get documents with pagination
        $documents = $query->orderBy('document_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get available years for filter
        $years = Document::active()
            ->accessibleBy($user)
            ->whereNotNull('document_date')
            ->selectRaw('YEAR(document_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Define variables for view
        $selectedCategory = $request->category;
        $searchTerm = $request->search;
        $selectedYear = $request->year;

        return view('documents.index', compact(
            'documents',
            'categories',
            'years',
            'selectedCategory',
            'searchTerm',
            'selectedYear'
        ));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get all active categories for uploading (not just root)
        $categories = DocumentCategory::with(['children' => function($query) {
            $query->active()->orderBy('sort_order');
        }])
        ->active()
        ->where(function($query) use ($user) {
            $query->whereNull('access_roles')
                  ->orWhereJsonContains('access_roles', $user->roles->pluck('name'));
        })
        ->orderBy('sort_order')
        ->get();

        return view('documents.create', compact('categories'));
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:document_categories,id',
            'document_date' => 'nullable|date',
            'reference' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'access_roles' => 'nullable|array',
            'is_public' => 'boolean',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $user = Auth::user();
        $category = DocumentCategory::findOrFail($validated['category_id']);

        // Check if user can upload to this category
        if (!$category->hasAccess($user)) {
            abort(403, 'No tienes permiso para subir documentos a esta categoría');
        }

        // Handle file upload
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store(date('Y/m'), 'documents');

        // Create slug
        $slug = \Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;

        while (Document::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Create document
        $document = Document::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $user->id,
            'access_roles' => $validated['access_roles'] ?? [],
            'is_public' => $validated['is_public'] ?? false,
            'document_date' => $validated['document_date'] ? Carbon::parse($validated['document_date']) : null,
            'reference' => $validated['reference'],
            'tags' => $validated['tags'],
        ]);

        return redirect()
            ->route('campus.documents.show', $document)
            ->with('success', 'Documento subido correctamente');
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document)
    {
        $user = Auth::user();

        // Check if user has access to this document
        if (!$document->hasAccess($user)) {
            abort(403, 'No tienes permiso para acceder a este documento');
        }

        // Get recent downloads
        $recentDownloads = $document->downloads()
            ->with('user')
            ->orderBy('downloaded_at', 'desc')
            ->take(10)
            ->get();

        return view('documents.show', compact('document', 'recentDownloads'));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(Document $document)
    {
        $user = Auth::user();

        // Check if user can edit (admin, super-admin, or uploader)
        if (!$user->hasAnyRole(['admin', 'super-admin']) && $document->uploaded_by !== $user->id) {
            abort(403, 'No tienes permiso para editar este documento');
        }

        // Get all active categories for editing (not just root)
        $categories = DocumentCategory::with(['children' => function($query) {
            $query->active()->orderBy('sort_order');
        }])
        ->active()
        ->orderBy('sort_order')
        ->get();

        return view('documents.edit', compact('document', 'categories'));
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, Document $document)
    {
        $user = Auth::user();

        // Check if user can edit
        if (!$user->hasAnyRole(['admin', 'super-admin']) && $document->uploaded_by !== $user->id) {
            abort(403, 'No tienes permiso para editar este documento');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:document_categories,id',
            'document_date' => 'nullable|date',
            'reference' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'access_roles' => 'nullable|array',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Update slug if title changed
        if ($validated['title'] !== $document->title) {
            $slug = \Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;

            while (Document::where('slug', $slug)->where('id', '!=', $document->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;
        }

        $document->update($validated);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Documento actualizado correctamente');
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Document $document)
    {
        $user = Auth::user();

        // Check if user can delete (admin, super-admin, or uploader)
        if (!$user->hasAnyRole(['admin', 'super-admin']) && $document->uploaded_by !== $user->id) {
            abort(403, 'No tienes permiso para eliminar este documento');
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Delete document
        $document->delete();

        return redirect()
            ->route('documents.index')
            ->with('success', 'Documento eliminado correctamente');
    }

    /**
     * Download the specified document.
     */
    public function download(Document $document)
    {
        $user = Auth::user();

        // Check if user has access to this document
        if (!$document->hasAccess($user)) {
            abort(403, 'No tienes permiso para descargar este documento');
        }

        // Record download
        $document->recordDownload(
            $user,
            request()->ip(),
            request()->userAgent()
        );

        // Return file download
        return Storage::disk('documents')->download($document->file_path, $document->file_name);
    }

    /**
     * Display documents for teacher (specialized view).
     */
    public function teacherIndex(Request $request)
    {
        $user = Auth::user();
        
        // Obtener cursos del profesor
        $teacherCourses = \App\Models\CampusCourse::whereHas('teachers', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->pluck('id', 'title');

        // Query para documentos del profesor
        $query = Document::with(['course', 'category'])
            ->where('teacher_id', $user->id)
            ->active();

        // Aplicar filtros específicos
        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $documents = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Estadísticas
        $stats = [
            'total_documents' => Document::where('teacher_id', $user->id)->active()->count(),
            'course_documents' => Document::where('teacher_id', $user->id)->whereNotNull('course_id')->active()->count(),
            'material_documents' => Document::where('teacher_id', $user->id)->where('document_type', 'material')->active()->count(),
            'task_documents' => Document::where('teacher_id', $user->id)->where('document_type', 'tarea')->active()->count(),
        ];

        // Años académicos disponibles
        $academicYears = Document::where('teacher_id', $user->id)
            ->whereNotNull('academic_year')
            ->selectRaw('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        return view('teacher.documents.index', compact(
            'documents',
            'teacherCourses',
            'stats',
            'academicYears'
        ));
    }

    /**
     * Show form for creating teacher document.
     */
    public function teacherCreate()
    {
        $user = Auth::user();
        
        // Obtener categorías para profesores
        $categories = DocumentCategory::where('slug', 'like', '%docente%')
            ->orWhere('slug', 'like', '%tarea%')
            ->orWhere('slug', 'like', '%evaluacion%')
            ->orWhere('slug', 'like', '%recurso%')
            ->active()
            ->orderBy('sort_order')
            ->get();

        // Obtener cursos del profesor
        $courses = \App\Models\CampusCourse::whereHas('teachers', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // Tipos de documento
        $documentTypes = [
            'material' => 'Material Educativo',
            'tarea' => 'Tarea/Actividad',
            'evaluacion' => 'Evaluación',
            'recurso' => 'Recurso Complementario',
        ];

        // Visibilidad para estudiantes
        $visibilityOptions = [
            'private' => 'Privado (solo yo)',
            'course' => 'Estudiantes del curso',
            'all' => 'Todos los estudiantes',
        ];

        return view('teacher.documents.create', compact(
            'categories',
            'courses',
            'documentTypes',
            'visibilityOptions'
        ));
    }

    /**
     * Store teacher document.
     */
    public function teacherStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:document_categories,id',
            'course_id' => 'nullable|exists:campus_courses,id',
            'document_type' => 'required|in:material,tarea,evaluacion,recurso',
            'student_visibility' => 'required|in:private,course,all',
            'academic_year' => 'nullable|integer|min:2000|max:2100',
            'document_date' => 'nullable|date',
            'tags' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $user = Auth::user();
        
        // Verificar que el curso pertenece al profesor
        if ($validated['course_id']) {
            $course = \App\Models\CampusCourse::find($validated['course_id']);
            if (!$course->teachers()->where('user_id', $user->id)->exists()) {
                return back()->with('error', 'No tienes permiso para subir documentos a este curso.');
            }
        }

        // Determinar año académico si no se especifica
        if (!isset($validated['academic_year'])) {
            $validated['academic_year'] = date('Y') >= 8 ? date('Y') + 1 : date('Y');
        }

        // Handle file upload
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store(date('Y/m'), 'documents');

        // Create slug
        $slug = \Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;

        while (Document::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Create document
        $document = Document::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $user->id,
            'teacher_id' => $user->id,
            'course_id' => $validated['course_id'],
            'document_type' => $validated['document_type'],
            'student_visibility' => $validated['student_visibility'],
            'academic_year' => $validated['academic_year'],
            'document_date' => $validated['document_date'] ? Carbon::parse($validated['document_date']) : null,
            'tags' => $validated['tags'],
            'is_public' => false, // Los documentos de profesor no son públicos por defecto
        ]);

        return redirect()
            ->route('teacher.documents.show', $document)
            ->with('success', 'Documento subido correctamente');
    }

    /**
     * Show teacher document.
     */
    public function teacherShow(Document $document)
    {
        $user = Auth::user();

        // Verificar que el documento pertenece al profesor
        if ($document->teacher_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a este documento');
        }

        // Obtener descargas recientes
        $recentDownloads = $document->downloads()
            ->with('user')
            ->orderBy('downloaded_at', 'desc')
            ->take(10)
            ->get();

        return view('teacher.documents.show', compact('document', 'recentDownloads'));
    }

    /**
     * Update teacher document.
     */
    public function teacherUpdate(Request $request, Document $document)
    {
        $user = Auth::user();

        // Verificar que el documento pertenece al profesor
        if ($document->teacher_id !== $user->id) {
            abort(403, 'No tienes permiso para editar este documento');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:document_categories,id',
            'course_id' => 'nullable|exists:campus_courses,id',
            'document_type' => 'required|in:material,tarea,evaluacion,recurso',
            'student_visibility' => 'required|in:private,course,all',
            'academic_year' => 'nullable|integer|min:2000|max:2100',
            'document_date' => 'nullable|date',
            'tags' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Verificar que el curso pertenece al profesor
        if ($validated['course_id']) {
            $course = \App\Models\CampusCourse::find($validated['course_id']);
            if (!$course->teachers()->where('user_id', $user->id)->exists()) {
                return back()->with('error', 'No tienes permiso para asignar este documento a este curso.');
            }
        }

        // Handle file replacement if provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store(date('Y/m'), 'documents');

            // Update file information
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $fileName;
            $validated['file_type'] = $file->getClientMimeType();
            $validated['file_size'] = $file->getSize();
        }

        // Update slug if title changed
        if ($validated['title'] !== $document->title) {
            $slug = \Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;

            while (Document::where('slug', $slug)->where('id', '!=', $document->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;
        }

        // Update document
        $document->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? $document->slug,
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'course_id' => $validated['course_id'],
            'document_type' => $validated['document_type'],
            'student_visibility' => $validated['student_visibility'],
            'academic_year' => $validated['academic_year'],
            'document_date' => $validated['document_date'] ? Carbon::parse($validated['document_date']) : null,
            'tags' => $validated['tags'],
            // Update file info only if new file provided
            'file_path' => $validated['file_path'] ?? $document->file_path,
            'file_name' => $validated['file_name'] ?? $document->file_name,
            'file_type' => $validated['file_type'] ?? $document->file_type,
            'file_size' => $validated['file_size'] ?? $document->file_size,
        ]);

        return redirect()
            ->route('teacher.documents.show', $document)
            ->with('success', 'Documento actualizado correctamente');
    }

    /**
     * Show form for editing teacher document.
     */
    public function teacherEdit(Document $document)
    {
        $user = Auth::user();

        // Verificar que el documento pertenece al profesor
        if ($document->teacher_id !== $user->id) {
            abort(403, 'No tienes permiso para editar este documento');
        }
        
        // Obtener categorías para profesores
        $categories = DocumentCategory::where('slug', 'like', '%docente%')
            ->orWhere('slug', 'like', '%tarea%')
            ->orWhere('slug', 'like', '%evaluacion%')
            ->orWhere('slug', 'like', '%recurso%')
            ->active()
            ->orderBy('sort_order')
            ->get();

        // Obtener cursos del profesor
        $courses = \App\Models\CampusCourse::whereHas('teachers', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // Tipos de documento
        $documentTypes = [
            'material' => 'Material Educativo',
            'tarea' => 'Tarea/Actividad',
            'evaluacion' => 'Evaluación',
            'recurso' => 'Recurso Complementario',
        ];

        // Visibilidad para estudiantes
        $visibilityOptions = [
            'private' => 'Privado (solo yo)',
            'course' => 'Estudiantes del curso',
            'all' => 'Todos los estudiantes',
        ];

        return view('teacher.documents.edit', compact(
            'document',
            'categories',
            'courses',
            'documentTypes',
            'visibilityOptions'
        ));
    }

    /**
     * Display documents for student (specialized view).
     */
    public function studentIndex(Request $request)
    {
        $user = Auth::user();

        // Obtener cursos del estudiante
        $studentCourses = $user->studentCourses()
            ->with('course')
            ->get();

        // Obtener IDs de cursos del estudiante
        $studentCourseIds = $user->studentCourses()->pluck('id')->toArray();
        
        // Query para documentos disponibles para el estudiante
        $query = Document::with(['course', 'category', 'teacher'])
            ->where(function($q) use ($user, $studentCourseIds) {
                // Documentos de profesor visibles para este estudiante
                $q->whereHas('teacher', function($subQuery) {
                    $subQuery->whereNotNull('id');
                })->where(function($visibilityQuery) use ($user, $studentCourseIds) {
                    // Documentos públicos para estudiantes
                    $visibilityQuery->where('student_visibility', 'all')
                        // Documentos del curso del estudiante
                        ->orWhere(function($courseQuery) use ($studentCourseIds) {
                            $courseQuery->where('student_visibility', 'course')
                                ->whereIn('course_id', $studentCourseIds);
                        });
                });
            })
            ->active();

        // Aplicar filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('tags', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Ordenar por fecha de creación descendente
        $documents = $query->orderBy('created_at', 'desc')->paginate(12);

        // Calcular estadísticas
        $stats = [
            'total_documents' => $query->count(),
            'course_documents' => $query->whereNotNull('course_id')->count(),
            'public_documents' => $query->where('student_visibility', 'all')->count(),
            'recent_downloads' => \App\Models\DocumentDownload::where('user_id', $user->id)
                ->where('downloaded_at', '>=', now()->subDays(7))
                ->count(),
        ];

        // Tipos de documento para filtros
        $documentTypes = [
            'material' => 'Material Educativo',
            'tarea' => 'Tarea/Actividad',
            'evaluacion' => 'Evaluación',
            'recurso' => 'Recurso Complementario',
        ];

        return view('student.documents.index', compact(
            'documents',
            'studentCourses',
            'courses',
            'stats',
            'documentTypes'
        ));
    }

    /**
     * Show student document.
     */
    public function studentShow(Document $document)
    {
        $user = Auth::user();

        // Verificar que el estudiante tiene acceso
        if (!$document->hasAccess($user)) {
            abort(403, 'No tienes permiso para acceder a este documento');
        }

        // Obtener descargas recientes
        $recentDownloads = $document->downloads()
            ->with('user')
            ->orderBy('downloaded_at', 'desc')
            ->take(10)
            ->get();

        return view('student.documents.show', compact('document', 'recentDownloads'));
    }

    /**
     * Download document for student.
     */
    public function studentDownload(Document $document)
    {
        $user = Auth::user();

        // Verificar acceso
        if (!$document->hasAccess($user)) {
            abort(403, 'No tienes permiso para descargar este documento');
        }

        // Registrar descarga
        $document->recordDownload(
            $user,
            request()->ip(),
            request()->userAgent()
        );

        // Descargar archivo
        return Storage::disk('documents')->download($document->file_path, $document->file_name);
    }

    /**
     * API endpoint for category documents.
     */
    public function getCategoryDocuments(Request $request, DocumentCategory $category)
    {
        $user = Auth::user();

        if (!$category->hasAccess($user)) {
            abort(403, 'No tienes permiso para acceder a esta categoría');
        }

        $documents = $category->allDocuments()
            ->active()
            ->accessibleBy($user)
            ->orderBy('document_date', 'desc')
            ->take(10)
            ->get(['id', 'title', 'file_name', 'document_date', 'created_at']);

        return response()->json($documents);
    }
}
