<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReleaseNote;
use Illuminate\Http\Request;

class ReleaseController extends Controller
{
    public function index(Request $request)
    {
        $releases = ReleaseNote::with(['createdBy', 'publishedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.releases.index', compact('releases'));
    }

    public function create()
    {
        return view('admin.releases.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'version' => 'required|string|max:50|unique:release_notes,version',
            'type' => 'required|in:major,minor,patch',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'features' => 'nullable|array',
            'improvements' => 'nullable|array',
            'fixes' => 'nullable|array',
            'breaking_changes' => 'nullable|array',
            'affected_modules' => 'nullable|array',
        ]);

        $release = ReleaseNote::create([
            'title' => $validated['title'],
            'version' => $validated['version'],
            'type' => $validated['type'],
            'summary' => $validated['summary'],
            'content' => $validated['content'],
            'features' => $validated['features'] ?? [],
            'improvements' => $validated['improvements'] ?? [],
            'fixes' => $validated['fixes'] ?? [],
            'breaking_changes' => $validated['breaking_changes'] ?? [],
            'affected_modules' => $validated['affected_modules'] ?? [],
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.releases.index')
            ->with('success', 'Release Note creat correctament.');
    }

    public function show(ReleaseNote $release)
    {
        return view('admin.releases.show', compact('release'));
    }

    public function edit(ReleaseNote $release)
    {
        return view('admin.releases.edit', compact('release'));
    }

    public function update(Request $request, ReleaseNote $release)
    {
        if ($release->isPublished()) {
            return redirect()
                ->route('admin.releases.show', $release)
                ->with('error', 'No es pot editar un release publicat.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'version' => 'required|string|max:50|unique:release_notes,version,' . $release->id,
            'type' => 'required|in:major,minor,patch',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'features' => 'nullable|array',
            'improvements' => 'nullable|array',
            'fixes' => 'nullable|array',
            'breaking_changes' => 'nullable|array',
            'affected_modules' => 'nullable|array',
        ]);

        $release->update($validated);

        return redirect()
            ->route('admin.releases.show', $release)
            ->with('success', 'Release Note actualitzat correctament.');
    }

    public function destroy(ReleaseNote $release)
    {
        if ($release->isPublished()) {
            return redirect()
                ->route('admin.releases.show', $release)
                ->with('error', 'No es pot eliminar un release publicat.');
        }

        $release->delete();

        return redirect()
            ->route('admin.releases.index')
            ->with('success', 'Release Note eliminat correctament.');
    }

    public function publish(ReleaseNote $release)
    {
        if ($release->isPublished()) {
            return redirect()
                ->route('admin.releases.show', $release)
                ->with('error', 'Aquest release ja està publicat.');
        }

        $release->status = 'published';
        $release->published_at = now();
        $release->published_by = auth()->id();
        $release->save();

        return redirect()
            ->route('admin.releases.show', $release)
            ->with('success', 'Release Note publicat correctament.');
    }

    public function archive(ReleaseNote $release)
    {
        if (!$release->isPublished()) {
            return redirect()
                ->route('admin.releases.show', $release)
                ->with('error', 'Només es poden arxivar releases publicats.');
        }

        $release->status = 'archived';
        $release->save();

        return redirect()
            ->route('admin.releases.show', $release)
            ->with('success', 'Release Note arxivat correctament.');
    }
}
