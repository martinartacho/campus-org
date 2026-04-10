@extends('campus.shared.layout')

@section('title', 'Tasques - Campus Virtual')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="taskManager()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tasques</h1>
                <p class="text-gray-600 mt-2">Gestiona els teus projectes i tasques</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('tasks.boards.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nou Tauler
                </a>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       x-model="search"
                       @input="searchBoards()"
                       placeholder="Cercar taulers..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select x-model="filterType" @change="filterBoards()" 
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Tots els tipus</option>
                <option value="course">Cursos</option>
                <option value="team">Equips</option>
                <option value="global">Globals</option>
                <option value="department">Departaments</option>
            </select>
        </div>
    </div>

    <!-- Grid de Taulers -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <template x-for="board in filteredBoards" :key="board.id">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer"
                 @click="openBoard(board.id)">
                <!-- Header del tauler -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="board.name"></h3>
                        <span class="px-2 py-1 text-xs rounded-full"
                              :class="getBoardTypeColor(board.type)"
                              x-text="getBoardTypeLabel(board.type)"></span>
                    </div>
                    <p class="text-sm text-gray-600 line-clamp-2" x-text="board.description || 'Sense descripció'"></p>
                </div>
                
                <!-- Estadístiques -->
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Tasques totals</span>
                            <div class="font-semibold" x-text="board.stats?.total_tasks || 0"></div>
                        </div>
                        <div>
                            <span class="text-gray-500">Completades</span>
                            <div class="font-semibold text-green-600" x-text="board.stats?.completed_tasks || 0"></div>
                        </div>
                        <div>
                            <span class="text-gray-500">En curs</span>
                            <div class="font-semibold text-blue-600" x-text="board.stats?.in_progress_tasks || 0"></div>
                        </div>
                        <div>
                            <span class="text-gray-500">Endarrerides</span>
                            <div class="font-semibold text-red-600" x-text="board.stats?.overdue_tasks || 0"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-user mr-1"></i>
                            <span x-text="board.creator?.name"></span>
                        </div>
                        <div class="text-xs text-gray-400">
                            <i class="fas fa-clock mr-1"></i>
                            <span x-text="formatDate(board.created_at)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Estat buit -->
    <div x-show="filteredBoards.length === 0" class="text-center py-12">
        <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No s'han trobat taulers</h3>
        <p class="text-gray-500 mb-4">Crea el teu primer tauler per començar a organitzar les teves tasques</p>
        <a href="{{ route('tasks.boards.create') }}" 
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
            <i class="fas fa-plus mr-2"></i>Crear Tauler
        </a>
    </div>
</div>

<script>
function taskManager() {
    return {
        boards: {!! $boards->toJson() !!},
        filteredBoards: [],
        search: '',
        filterType: '',
        loading: false,

        init() {
            this.filteredBoards = [...this.boards];
        },

        searchBoards() {
            this.applyFilters();
        },

        filterBoards() {
            this.applyFilters();
        },

        applyFilters() {
            this.filteredBoards = this.boards.filter(board => {
                const matchesSearch = !this.search || 
                    board.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    (board.description && board.description.toLowerCase().includes(this.search.toLowerCase()));
                
                const matchesType = !this.filterType || board.type === this.filterType;
                
                return matchesSearch && matchesType;
            });
        },

        openBoard(boardId) {
            window.location.href = `/tasques/tauler/${boardId}`;
        },

        getBoardTypeColor(type) {
            const colors = {
                'course': 'bg-blue-100 text-blue-800',
                'team': 'bg-green-100 text-green-800',
                'global': 'bg-purple-100 text-purple-800',
                'department': 'bg-orange-100 text-orange-800'
            };
            return colors[type] || 'bg-gray-100 text-gray-800';
        },

        getBoardTypeLabel(type) {
            const labels = {
                'course': 'Curs',
                'team': 'Equip',
                'global': 'Global',
                'department': 'Departament'
            };
            return labels[type] || type;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ca-ES', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }
    }
}
</script>
@endsection
