@extends('campus.shared.layout')

@section('title', 'Crear Tauler - Tasques')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl" x-data="boardCreator()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tasks.boards.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Tornar a Tasques
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Crear Nou Tauler</h1>
                <p class="text-gray-600 mt-1">Configura el teu nou tauler de tasques</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('tasks.boards.store') }}">
            @csrf
            
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
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ex: Coordinació General">
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
                              placeholder="Descripció opcional del tauler...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipus -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipus de Tauler <span class="text-red-500">*</span>
                    </label>
                    <select id="type" 
                            name="type" 
                            required
                            x-data="{ selectedType: '{{ old('type', 'team') }}' }"
                            x-model="selectedType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="team" {{ old('type') == 'team' ? 'selected' : '' }}>Equip</option>
                        <option value="course" {{ old('type') == 'course' ? 'selected' : '' }}>Curs</option>
                        <option value="department" {{ old('type') == 'department' ? 'selected' : '' }}>Departament</option>
                        <option value="global" {{ old('type') == 'global' ? 'selected' : '' }}>Global</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <!-- Descripcions dels tipus -->
                    <div class="mt-2 text-sm text-gray-600">
                        <template x-if="selectedType === 'team'">
                            <div>
                                <i class="fas fa-users mr-1"></i>
                                Per a equips interns, visible només per membres de l'equip
                            </div>
                        </template>
                        <template x-if="selectedType === 'course'">
                            <div>
                                <i class="fas fa-graduation-cap mr-1"></i>
                                Vinculat a un curs específic, visible per participants
                            </div>
                        </template>
                        <template x-if="selectedType === 'department'">
                            <div>
                                <i class="fas fa-building mr-1"></i>
                                Per a departaments, amb accés per rols
                            </div>
                        </template>
                        <template x-if="selectedType === 'global'">
                            <div>
                                <i class="fas fa-globe mr-1"></i>
                                Visible per tot el campus (només administradors)
                            </div>
                        </template>
                    </div>
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
                        <option value="">Selecciona una opció</option>
                        <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Privat</option>
                        <option value="team" {{ old('visibility') == 'team' ? 'selected' : '' }}>Equip</option>
                        <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>Públic</option>
                    </select>
                    @error('visibility')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <!-- Descripcions de visibilitat -->
                    <div class="mt-2 text-sm text-gray-600">
                        <template x-if="selectedVisibility === 'private'">
                            <div>
                                <i class="fas fa-lock mr-1"></i>
                                Només visible per tu
                            </div>
                        </template>
                        <template x-if="selectedVisibility === 'team'">
                            <div>
                                <i class="fas fa-users mr-1"></i>
                                Visible per l'equip del tauler
                            </div>
                        </template>
                        <template x-if="selectedVisibility === 'public'">
                            <div>
                                <i class="fas fa-eye mr-1"></i>
                                Visible per tots els usuaris del campus
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Responsable del Tauler -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable del Tauler</label>
                    
                    <!-- Mode de selecció -->
                    <div class="mb-2">
                        <label class="inline-flex items-center">
                            <input type="radio" x-model="assignmentMode" value="email" class="mr-2">
                            <span class="text-sm">Cercar per correu o codi</span>
                        </label>
                        <label class="inline-flex items-center ml-4">
                            <input type="radio" x-model="assignmentMode" value="role" class="mr-2">
                            <span class="text-sm">Seleccionar per rol</span>
                        </label>
                    </div>
                    
                    <!-- Cerca per correu/codi -->
                    <div x-show="assignmentMode === 'email'" class="space-y-2">
                        <input type="text" 
                               x-model="userSearch"
                               @input.debounce.300ms="searchUsers"
                               placeholder="Escriu correu electrònic o nom..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        
                        <div x-show="searchResults.length > 0" class="border border-gray-200 rounded-lg max-h-32 overflow-y-auto">
                            <template x-for="user in searchResults" :key="user.id">
                                <div @click="selectUser(user)" 
                                     class="px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <div class="font-medium" x-text="user.name"></div>
                                    <div class="text-sm text-gray-500" x-text="user.email"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Selecció per rol -->
                    <div x-show="assignmentMode === 'role'" class="space-y-2">
                        <select x-model="selectedRole" 
                                @change="loadRoleUsers"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Selecciona un rol</option>
                            <option value="admin">Administrador ({{ count($usersByRole['admin'] ?? []) }})</option>
                            <option value="teacher">Professorat ({{ count($usersByRole['teacher'] ?? []) }})</option>
                            <option value="student">Alumnes ({{ count($usersByRole['student'] ?? []) }})</option>
                            <option value="secretaria">Secretaria ({{ count($usersByRole['secretaria'] ?? []) }})</option>
                            <option value="coordinacio">Coordinació ({{ count($usersByRole['coordinacio'] ?? []) }})</option>
                            <option value="editor">Editor ({{ count($usersByRole['editor'] ?? []) }})</option>
                            <option value="treasury">Treasury ({{ count($usersByRole['treasury'] ?? []) }})</option>
                            <option value="gestio">Gestió ({{ count($usersByRole['gestio'] ?? []) }})</option>
                            <option value="director">Director ({{ count($usersByRole['director'] ?? []) }})</option>
                            <option value="manager">Manager ({{ count($usersByRole['manager'] ?? []) }})</option>
                            <option value="comunicacio">Comunicació ({{ count($usersByRole['comunicacio'] ?? []) }})</option>
                        </select>
                        
                        <div x-show="roleUsers.length > 0" class="border border-gray-200 rounded-lg max-h-32 overflow-y-auto">
                            <template x-for="user in roleUsers" :key="user.id">
                                <div @click="selectUser(user)" 
                                     class="px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <div class="font-medium" x-text="user.name"></div>
                                    <div class="text-sm text-gray-500" x-text="user.email"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Usuari seleccionat -->
                    <div x-show="selectedUser" class="mt-2 p-2 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium" x-text="selectedUser?.name"></div>
                                <div class="text-sm text-gray-600" x-text="selectedUser?.email"></div>
                            </div>
                            <button type="button" @click="clearSelection()" 
                                    class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Camp ocult per enviar l'ID -->
                    <input type="hidden" name="responsible_id" :value="selectedUser ? selectedUser.id : ''">
                </div>

                <!-- Entity ID (per cursos/departaments) -->
                <div x-show="selectedType === 'course' || selectedType === 'department'">
                    <label for="entity_id" class="block text-sm font-medium text-gray-700 mb-1">
                        <template x-if="selectedType === 'course'">
                            Curs
                        </template>
                        <template x-if="selectedType === 'department'">
                            Departament
                        </template>
                    </label>
                    <select id="entity_id" 
                            name="entity_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Selecciona...</option>
                        <!-- TODO: Carregar cursos o departaments dinàmicament -->
                    </select>
                    @error('entity_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Opcions avançades -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Opcions Avançades</h3>
                    
                    <div class="space-y-4">
                        <!-- Crear llistes per defecte -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="create_default_lists" 
                                   name="create_default_lists" 
                                   value="1"
                                   checked
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="create_default_lists" class="ml-2 text-sm text-gray-700">
                                Crear llistes per defecte (Pendents, En curs, Bloquejat, Fet)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botons -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('tasks.boards.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel·lar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Crear Tauler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function boardCreator() {
    return {
        selectedVisibility: '{{ old('visibility', 'team') }}',
        selectedType: '{{ old('type', 'general') }}',
        
        // Propietats per assignació d'usuaris
        assignmentMode: 'email',
        userSearch: '',
        searchResults: [],
        selectedRole: '',
        roleUsers: [],
        selectedUser: null,
        
        init() {
            // Carregar usuaris per rol si es necessita
            this.loadUsers();
        },
        
        async loadUsers() {
            try {
                const response = await fetch('/api/users/by-role');
                const users = await response.json();
                this.usersByRole = users;
            } catch (error) {
                console.error('Error carregant usuaris:', error);
            }
        },
        
        searchUsers() {
            if (this.userSearch.length < 2) {
                this.searchResults = [];
                return;
            }
            
            const searchTerm = this.userSearch.toLowerCase();
            this.searchResults = [];
            
            // Cercar en tots els usuaris
            Object.values(this.usersByRole || {}).forEach(users => {
                users.forEach(user => {
                    if (user.name.toLowerCase().includes(searchTerm) || 
                        user.email.toLowerCase().includes(searchTerm)) {
                        this.searchResults.push(user);
                    }
                });
            });
            
            // Limitar resultats
            this.searchResults = this.searchResults.slice(0, 10);
        },
        
        async loadRoleUsers() {
            if (!this.selectedRole) {
                this.roleUsers = [];
                return;
            }
            
            try {
                const response = await fetch(`/api/users/role/${this.selectedRole}`);
                this.roleUsers = await response.json();
            } catch (error) {
                console.error('Error carregant usuaris del rol:', error);
            }
        },
        
        selectUser(user) {
            if (user && user.id) {
                this.selectedUser = user;
                this.searchResults = [];
                this.userSearch = '';
            }
        },
        
        clearSelection() {
            this.selectedUser = null;
        }
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
