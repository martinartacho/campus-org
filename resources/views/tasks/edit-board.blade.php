@extends('campus.shared.layout')

@section('title', 'Editar Tauler - ' . $board->name)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tasks.boards.show', $board) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Tornar al Tauler
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Tauler</h1>
                <p class="text-gray-600 mt-1">Modifica la configuració del tauler</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('tasks.boards.update', $board) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom del Tauler <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required
                           value="{{ old('name', $board->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripció -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripció
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Descripció opcional del tauler...">{{ old('description', $board->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Visibilitat -->
                <div>
                    <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">
                        Visibilitat <span class="text-red-500">*</span>
                    </label>
                    <select id="visibility" 
                            name="visibility" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="private" {{ old('visibility', $board->visibility) == 'private' ? 'selected' : '' }}>Privat</option>
                        <option value="team" {{ old('visibility', $board->visibility) == 'team' ? 'selected' : '' }}>Equip</option>
                        <option value="public" {{ old('visibility', $board->visibility) == 'public' ? 'selected' : '' }}>Públic</option>
                    </select>
                    @error('visibility')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informació del tauler -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Informació del Tauler</h3>
                    <div class="space-y-1 text-sm text-gray-600">
                        <div><strong>Tipus:</strong> {{ __('tasks.board_types.' . $board->type, $board->type) }}</div>
                        <div><strong>Creat per:</strong> {{ $board->creator->name }}</div>
                        <div><strong>Creat el:</strong> {{ $board->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Tasques totals:</strong> {{ $board->tasks()->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Botons -->
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <div>
                    <button type="button" 
                            onclick="if(confirm('Estàs segur que vols eliminar aquest tauler?')) { document.getElementById('delete-form').submit(); }"
                            class="px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Eliminar Tauler
                    </button>
                    <form id="delete-form" action="{{ route('tasks.boards.destroy', $board) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
                
                <div class="flex space-x-3">
                    <a href="{{ route('tasks.boards.show', $board) }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel·lar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Desar Canvis
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
