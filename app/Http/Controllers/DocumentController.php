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
        
        // Get accessible categories for uploading
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

        $categories = DocumentCategory::with(['children' => function($query) {
            $query->active()->orderBy('sort_order');
        }])
        ->root()
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
        return Storage::disk('public')->download($document->file_path, $document->file_name);
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
