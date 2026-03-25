@extends('campus.shared.layout')

@section('title', 'Editar Release: ' . $release->title)
@section('subtitle', 'Release Notes - Administració')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Release</h1>
            <p class="text-gray-600 mt-2">{{ $release->title }} (v{{ $release->version }})</p>
        </div>
        
        <div class="flex gap-2">
            @if($release->isDraft())
                <form action="{{ route('admin.releases.publish', $release) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <i class="bi bi-check-circle mr-2"></i>Publicar
                    </button>
                </form>
            @endif
            
            <a href="{{ route('admin.releases.show', $release) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                <i class="bi bi-eye mr-2"></i>Veure
            </a>
            
            <a href="{{ route('admin.releases.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                <i class="bi bi-arrow-left mr-2"></i>Tornar
            </a>
        </div>
    </div>

    @if($release->isPublished())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <i class="bi bi-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-800">⚠️ Release Publicat</h3>
                    <p class="text-yellow-700">Aquest release ja està publicat i no es pot editar. Si necessites fer canvis, crea una nova versió.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Formulari -->
    <form action="{{ route('admin.releases.update', $release) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <!-- Informació bàsica -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Títol *</label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title', $release->title) }}"
                           @if($release->isPublished()) disabled @endif
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $release->isPublished() ? 'bg-gray-100' : '' }}">
                    @error('title')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Versió *</label>
                    <input type="text" 
                           name="version" 
                           value="{{ old('version', $release->version) }}"
                           @if($release->isPublished()) disabled @endif
                           placeholder="ex: 1.2.3"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $release->isPublished() ? 'bg-gray-100' : '' }}">
                    @error('version')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipus *</label>
                    <select name="type" 
                            @if($release->isPublished()) disabled @endif
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $release->isPublished() ? 'bg-gray-100' : '' }}">
                        <option value="major" {{ old('type', $release->type) == 'major' ? 'selected' : '' }}>Major (🔴)</option>
                        <option value="minor" {{ old('type', $release->type) == 'minor' ? 'selected' : '' }}>Minor (🟡)</option>
                        <option value="patch" {{ old('type', $release->type) == 'patch' ? 'selected' : '' }}>Patch (🟢)</option>
                    </select>
                    @error('type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Summary</label>
                    <input type="text" 
                           name="summary" 
                           value="{{ old('summary', $release->summary) }}"
                           @if($release->isPublished()) disabled @endif
                           placeholder="Resum breu del release"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $release->isPublished() ? 'bg-gray-100' : '' }}">
                    @error('summary')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contingut -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Contingut *</label>
                <textarea name="content" 
                          rows="12"
                          @if($release->isPublished()) disabled @endif
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm {{ $release->isPublished() ? 'bg-gray-100' : '' }}">{{ old('content', $release->content) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Pots utilitzar Markdown per formatar el text</p>
                @error('content')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mòduls afectats -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Mòduls Afectats</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="affected_modules[]" value="admin" 
                               @if($release->isPublished()) disabled @endif
                               {{ in_array('admin', old('affected_modules', $release->affected_modules ?? [])) ? 'checked' : '' }}
                               class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>Admin</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="affected_modules[]" value="campus" 
                               @if($release->isPublished()) disabled @endif
                               {{ in_array('campus', old('affected_modules', $release->affected_modules ?? [])) ? 'checked' : '' }}
                               class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>Campus</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="affected_modules[]" value="dashboard" 
                               @if($release->isPublished()) disabled @endif
                               {{ in_array('dashboard', old('affected_modules', $release->affected_modules ?? [])) ? 'checked' : '' }}
                               class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>Dashboard</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="affected_modules[]" value="help" 
                               @if($release->isPublished()) disabled @endif
                               {{ in_array('help', old('affected_modules', $release->affected_modules ?? [])) ? 'checked' : '' }}
                               class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>Help</span>
                    </label>
                </div>
            </div>

            <!-- Canvis disruptius -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Canvis Disruptius</label>
                <textarea name="breaking_changes" 
                          rows="3"
                          @if($release->isPublished()) disabled @endif
                          placeholder="Descriu els canvis disruptius (un per línia)"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $release->isPublished() ? 'bg-gray-100' : '' }}">{{ old('breaking_changes', implode("\n", $release->breaking_changes ?? [])) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Un canvi disruptiu per línia</p>
            </div>

            <!-- Botons d'acció -->
            @if(!$release->isPublished())
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.releases.show', $release) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        Cancel·lar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="bi bi-save mr-2"></i>Desar Canvis
                    </button>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection
