@extends('campus.shared.layout')

@section('title', $board->name . ' - Tasques')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="kanbanBoard({{ $board->id }})">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="/tasques" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Tornar a Tasques
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900" x-text="board.name"></h1>
                    <p class="text-gray-600 mt-1" x-text="board.description"></p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button @click="showCreateTaskModal = true" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nova Tasca
                </button>
                <button @click="showBoardSettings = true" 
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-cog mr-2"></i>Configuració
                </button>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="overflow-x-auto">
        <div class="flex space-x-4 min-w-max pb-4" style="min-height: 600px;">
            <template x-for="list in lists" :key="list.id">
                <div class="w-80 bg-gray-50 rounded-lg">
                    <!-- Header de la llista -->
                    <div class="p-4 border-b border-gray-200" 
                         :style="{ backgroundColor: list.color + '20', borderColor: list.color }">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900" x-text="list.name"></h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500" 
                                      x-text="`${list.tasks?.length || 0} tasques`"></span>
                                <button @click="showAddListModal = true; editingList = list" 
                                        class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks container -->
                    <div class="p-3 space-y-2 min-h-[400px] task-container" 
                         x-data="{ listId: list.id }"
                         :data-listId="list.id"
                         @dragover.prevent
                         @drop="handleDrop($event, listId)">
                        
                        <template x-for="task in getTasksForList(list.id)" :key="task.id">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 cursor-move hover:shadow-md transition-shadow"
                                 draggable="true"
                                 @dragstart="handleDragStart($event, task)"
                                 @click="openTaskModal(task)">
                                
                                <!-- Task priority indicator -->
                                <div class="w-1 h-full absolute left-0 top-0 rounded-l-lg"
                                     :style="{ backgroundColor: getPriorityColor(task.priority) }"
                                     :class="task.priority === 'urgent' ? 'animate-pulse' : ''"></div>
                                
                                <div class="pl-2">
                                    <!-- Task title -->
                                    <h4 class="font-medium text-gray-900 mb-2" x-text="task.title"></h4>
                                    
                                    <!-- Task description preview -->
                                    <p x-show="task.description" 
                                       class="text-sm text-gray-600 mb-2 line-clamp-2" 
                                       x-text="task.description"></p>
                                    
                                    <!-- Task meta -->
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        <!-- Priority -->
                                        <span class="px-2 py-1 rounded-full text-xs font-medium"
                                              :class="getPriorityBadgeClass(task.priority)"
                                              x-text="getPriorityLabel(task.priority)"></span>
                                        
                                        <!-- Due date -->
                                        <template x-if="task.due_date">
                                            <span class="flex items-center text-gray-500"
                                                  :class="isOverdue(task.due_date) ? 'text-red-500 font-medium' : ''">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                <span x-text="formatDate(task.due_date)"></span>
                                            </span>
                                        </template>
                                        
                                        <!-- Assigned user -->
                                        <template x-if="task.assigned_user">
                                            <div class="flex items-center">
                                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-medium"
                                                     x-text="getInitials(task.assigned_user.name)"></div>
                                                <span class="ml-1 text-gray-600" x-text="task.assigned_user.name"></span>
                                            </div>
                                        </template>
                                        
                                        <!-- Checklist progress -->
                                        <template x-if="task.checklist_count > 0">
                                            <div class="flex items-center text-gray-500">
                                                <i class="fas fa-check-square mr-1"></i>
                                                <span x-text="`${task.completed_checklist_count}/${task.checklist_count}`"></span>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <!-- Comments indicator -->
                                    <div x-show="task.comments_count > 0" class="mt-2 pt-2 border-t border-gray-100">
                                        <div class="flex items-center text-xs text-gray-500">
                                            <i class="fas fa-comments mr-1"></i>
                                            <span x-text="`${task.comments_count} comentaris`"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Add task button -->
                        <button @click="showCreateTaskModal = true; selectedListId = list.id"
                                class="w-full p-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Afegir tasca
                        </button>
                    </div>
                </div>
            </template>

            <!-- Add new list button -->
            <div class="w-80 flex-shrink-0">
                <button @click="showAddListModal = true"
                        class="w-full p-4 bg-white border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-gray-400 hover:text-gray-600 transition-colors">
                    <div class="text-center">
                        <i class="fas fa-plus text-xl mb-2"></i>
                        <p class="text-sm">Afegir llista</p>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Create Task Modal -->
    <div x-show="showCreateTaskModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showCreateTaskModal = false">
        
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-4">Crear Nova Tasca</h2>
                
                <form @submit.prevent="createTask">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Títol</label>
                            <input type="text" x-model="newTask.title" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripció</label>
                            <textarea x-model="newTask.description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Llista</label>
                                <select x-model="newTask.list_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Selecciona una llista</option>
                                    <template x-for="list in lists" :key="list.id">
                                        <option :value="list.id" x-text="list.name"></option>
                                    </template>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prioritat</label>
                                <select x-model="newTask.priority" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="low">Baixa</option>
                                    <option value="medium">Mitjana</option>
                                    <option value="high">Alta</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data d'inici</label>
                                <input type="date" x-model="newTask.start_date"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data de venciment</label>
                                <input type="date" x-model="newTask.due_date"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assignar a</label>
                            
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
                                    @foreach($usersByRole as $roleName => $users)
                                        <option value="{{ $roleName }}">{{ $roleName }} ({{ count($users) }} usuaris)</option>
                                    @endforeach
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
                                        <div class="font-medium" x-text="selectedUser.name"></div>
                                        <div class="text-sm text-gray-600" x-text="selectedUser.email"></div>
                                    </div>
                                    <button type="button" @click="clearSelection()" 
                                            class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showCreateTaskModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancel·lar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Crear Tasca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
function kanbanBoard(boardId) {
    return {
        boardId: boardId,
        board: {!! $board->toJson() !!},
        lists: {!! $board->lists->toJson() !!},
        tasks: {!! $board->lists->flatMap->tasks->toJson() !!},
        loading: false,
        showCreateTaskModal: false,
        showAddListModal: false,
        showBoardSettings: false,
        selectedListId: null,
        editingList: null,
        draggedTask: null,
        newTask: {
            title: '',
            description: '',
            list_id: '',
            priority: 'medium',
            start_date: '',
            due_date: '',
            assigned_to: ''
        },
        
        // Propietats per assignació d'usuaris
        assignmentMode: 'email',
        userSearch: '',
        searchResults: [],
        selectedRole: '',
        roleUsers: [],
        selectedUser: null,
        usersByRole: {!! $usersByRole->toJson() !!},

        init() {
            // Les dades ja estan carregades des de Laravel
            this.initSortable();
        },

        initSortable() {
            // Inicialitzar SortableJS per a cada llista
            this.$nextTick(() => {
                document.querySelectorAll('[x-data*="listId"]').forEach(container => {
                    new Sortable(container, {
                        group: 'tasks',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        onEnd: (evt) => {
                            const taskId = evt.item.dataset.taskId;
                            const newListId = evt.to.dataset.listId;
                            const newIndex = evt.newIndex;
                            this.moveTask(taskId, newListId, newIndex);
                        }
                    });
                });
            });
        },

        getTasksForList(listId) {
            return this.tasks.filter(task => task.list_id === listId);
        },

        handleDragStart(event, task) {
            this.draggedTask = task;
            event.target.dataset.taskId = task.id;
        },

        handleDrop(event, listId) {
            event.preventDefault();
            if (this.draggedTask) {
                this.moveTask(this.draggedTask.id, listId, 0);
            }
        },

        async moveTask(taskId, newListId, newIndex) {
            try {
                const response = await fetch(`/api/tasks/${taskId}/move`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        list_id: newListId,
                        order: newIndex
                    })
                });
                
                if (response.ok) {
                    // Actualitzar la tasca localment
                    const task = this.tasks.find(t => t.id === taskId);
                    if (task) {
                        task.list_id = newListId;
                        task.order_in_list = newIndex;
                    }
                }
            } catch (error) {
                console.error('Error movent la tasca:', error);
            }
        },

        async createTask() {
            try {
                const response = await fetch('/api/tasks', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.newTask)
                });
                
                if (response.ok) {
                    const task = await response.json();
                    this.tasks.push(task);
                    this.showCreateTaskModal = false;
                    this.resetNewTask();
                }
            } catch (error) {
                console.error('Error creant la tasca:', error);
            }
        },

        resetNewTask() {
            this.newTask = {
                title: '',
                description: '',
                list_id: this.selectedListId || '',
                priority: 'medium',
                start_date: '',
                due_date: '',
                assigned_to: ''
            };
        },

        openTaskModal(task) {
            // TODO: Implementar modal de detall de tasca
            console.log('Obrir tasca:', task);
        },

        getPriorityColor(priority) {
            const colors = {
                'low': '#6B7280',
                'medium': '#F59E0B',
                'high': '#EF4444',
                'urgent': '#DC2626'
            };
            return colors[priority] || '#6B7280';
        },

        getPriorityBadgeClass(priority) {
            const classes = {
                'low': 'bg-gray-100 text-gray-800',
                'medium': 'bg-yellow-100 text-yellow-800',
                'high': 'bg-red-100 text-red-800',
                'urgent': 'bg-red-100 text-red-800 animate-pulse'
            };
            return classes[priority] || 'bg-gray-100 text-gray-800';
        },

        getPriorityLabel(priority) {
            const labels = {
                'low': 'Baixa',
                'medium': 'Mitjana',
                'high': 'Alta',
                'urgent': 'Urgent'
            };
            return labels[priority] || priority;
        },

        isOverdue(dueDate) {
            return new Date(dueDate) < new Date() && !this.isCompleted;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ca-ES', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        },

        // Funcions per assignació d'usuaris
        searchUsers() {
            if (this.userSearch.length < 2) {
                this.searchResults = [];
                return;
            }
            
            const searchTerm = this.userSearch.toLowerCase();
            this.searchResults = [];
            
            // Cercar en tots els usuaris
            Object.values(this.usersByRole).forEach(users => {
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
        
        loadRoleUsers() {
            if (!this.selectedRole) {
                this.roleUsers = [];
                return;
            }
            this.roleUsers = this.usersByRole[this.selectedRole] || [];
        },
        
        selectUser(user) {
            this.selectedUser = user;
            this.newTask.assigned_to = user.id;
            this.searchResults = [];
            this.userSearch = '';
        },
        
        clearSelection() {
            this.selectedUser = null;
            this.newTask.assigned_to = '';
        },

        getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        }
    }
}
</script>
@endsection
