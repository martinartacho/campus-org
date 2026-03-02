<template>
    <div>
        <!-- Botón de Ajuda flotante -->
        <button
            @click="toggleHelp"
            class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-3 shadow-lg transition-all duration-200 z-50 group"
            :class="{ 'scale-110': isOpen }"
        >
            <i class="bi bi-question-lg text-xl"></i>
            <span class="absolute right-full mr-2 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
                Ajuda
            </span>
        </button>

        <!-- Modal de Ajuda -->
        <div
            v-if="isOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click="closeHelp"
        >
            <div
                class="bg-white rounded-lg max-w-4xl w-full max-h-[80vh] overflow-hidden shadow-2xl"
                @click.stop
            >
                <!-- Header -->
                <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold flex items-center">
                        <i class="bi bi-question-circle mr-2"></i>
                        Centre d'Ajuda
                    </h2>
                    <button
                        @click="closeHelp"
                        class="text-white hover:text-gray-200 transition-colors"
                    >
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>

                <!-- Contenido -->
                <div class="flex h-[60vh]">
                    <!-- Sidebar - Áreas y categorías -->
                    <div class="w-1/3 bg-gray-50 border-r border-gray-200 overflow-y-auto">
                        <!-- Áreas -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-700 mb-3">Àrees d'Ajuda</h3>
                            <div class="space-y-2">
                                <button
                                    v-for="area in areas"
                                    :key="area.key"
                                    @click="selectArea(area.key)"
                                    class="w-full text-left p-3 rounded-lg transition-colors"
                                    :class="{
                                        'bg-blue-100 text-blue-700 border-blue-300': selectedArea === area.key,
                                        'bg-white hover:bg-gray-100': selectedArea !== area.key
                                    }"
                                >
                                    <i :class="area.icon" class="mr-2"></i>
                                    {{ area.name }}
                                </button>
                            </div>
                        </div>

                        <!-- Categorías del área seleccionada -->
                        <div v-if="selectedArea && categories.length > 0" class="p-4 border-t border-gray-200">
                            <h3 class="font-semibold text-gray-700 mb-3">Categorías</h3>
                            <div class="space-y-1">
                                <button
                                    v-for="category in categories"
                                    :key="category.id"
                                    @click="selectCategory(category)"
                                    class="w-full text-left p-2 rounded text-sm transition-colors hover:bg-gray-100"
                                >
                                    <i :class="category.icon" class="mr-2 text-sm"></i>
                                    {{ category.name }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="flex-1 flex flex-col">
                        <!-- Barra de búsqueda -->
                        <div class="p-4 border-b border-gray-200">
                            <div class="relative">
                                <input
                                    v-model="searchQuery"
                                    @input="searchArticles"
                                    type="text"
                                    placeholder="Buscar Ajuda..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Artículos -->
                        <div class="flex-1 overflow-y-auto p-4">
                            <!-- Artículos contextuales -->
                            <div v-if="!searchQuery && contextualArticles.length > 0" class="mb-6">
                                <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                                    <i class="bi bi-lightning mr-2 text-yellow-500"></i>
                                    Ajuda per a aquesta pàgina
                                </h3>
                                <div class="space-y-2">
                                    <div
                                        v-for="article in contextualArticles"
                                        :key="article.id"
                                        @click="selectArticle(article)"
                                        class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg cursor-pointer hover:bg-yellow-100 transition-colors"
                                    >
                                        <h4 class="font-medium text-gray-800">{{ article.title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ article.content.substring(0, 100) }}...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Artículos del área -->
                            <div v-if="areaArticles.length > 0">
                                <h3 class="font-semibold text-gray-700 mb-3">
                                    <i :class="areas[selectedArea]?.icon" class="mr-2"></i>
                                    {{ areas[selectedArea]?.name }}
                                </h3>
                                <div class="space-y-2">
                                    <div
                                        v-for="article in areaArticles"
                                        :key="article.id"
                                        @click="selectArticle(article)"
                                        class="p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                                    >
                                        <h4 class="font-medium text-gray-800">{{ article.title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ article.content.substring(0, 100) }}...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Resultados de búsqueda -->
                            <div v-if="searchQuery && searchResults.length > 0">
                                <h3 class="font-semibold text-gray-700 mb-3">
                                    Resultados de búsqueda
                                </h3>
                                <div class="space-y-2">
                                    <div
                                        v-for="article in searchResults"
                                        :key="article.id"
                                        @click="selectArticle(article)"
                                        class="p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                                    >
                                        <h4 class="font-medium text-gray-800">{{ article.title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ article.content }}</p>
                                        <div class="flex items-center mt-2 text-xs text-gray-500">
                                            <span class="bg-gray-100 px-2 py-1 rounded">{{ article.area }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sin resultados -->
                            <div v-if="!contextualArticles.length && !areaArticles.length && (!searchQuery || !searchResults.length)" class="text-center py-8">
                                <i class="bi bi-search text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No s'ha trobat cap article ajuda</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <span v-if="selectedArticle">
                            Artículo: {{ selectedArticle.title }}
                        </span>
                        <span v-else>
                            Selecciona un artículo para ver el contenido
                        </span>
                    </div>
                    <button
                        v-if="selectedArticle"
                        @click="openFullArticle"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors"
                    >
                        Ver artículo completo
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ClassicHelpButton',
    data() {
        return {
            isOpen: false,
            selectedArea: null,
            selectedCategory: null,
            selectedArticle: null,
            searchQuery: '',
            areas: {
                cursos: { name: 'Cursos', icon: 'bi-book', key: 'cursos' },
                matricula: { name: 'Matrícula', icon: 'bi-person-plus', key: 'matricula' },
                materiales: { name: 'Materiales', icon: 'bi-folder', key: 'materiales' },
                configuracion: { name: 'Configuración', icon: 'bi-gear', key: 'configuracion' }
            },
            categories: [],
            contextualArticles: [],
            areaArticles: [],
            searchResults: [],
            currentPath: window.location.pathname
        }
    },
    mounted() {
        this.loadHelpData();
    },
    methods: {
        toggleHelp() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadHelpData();
            }
        },
        closeHelp() {
            this.isOpen = false;
        },
        async loadHelpData() {
            try {
                const response = await fetch(`/api/help/contextual?current_path=${this.currentPath}`);
                const data = await response.json();
                
                this.selectedArea = data.current_area;
                this.categories = data.categories || [];
                this.contextualArticles = data.contextual_articles || [];
                
                // Cargar artículos del área actual
                await this.loadAreaArticles(this.selectedArea);
            } catch (error) {
                console.error('Error loading help data:', error);
            }
        },
        async loadAreaArticles(area) {
            try {
                const response = await fetch(`/api/help/area/${area}`);
                const data = await response.json();
                this.areaArticles = data.articles || [];
            } catch (error) {
                console.error('Error loading area articles:', error);
                this.areaArticles = [];
            }
        },
        async selectArea(area) {
            this.selectedArea = area;
            this.selectedCategory = null;
            await this.loadAreaArticles(area);
        },
        selectCategory(category) {
            this.selectedCategory = category;
            // Filtrar artículos por categoría si es necesario
        },
        selectArticle(article) {
            this.selectedArticle = article;
        },
        async searchArticles() {
            if (!this.searchQuery.trim()) {
                this.searchResults = [];
                return;
            }
            
            try {
                const response = await fetch(`/api/help/search?q=${encodeURIComponent(this.searchQuery)}&area=${this.selectedArea || ''}`);
                const data = await response.json();
                this.searchResults = data.articles || [];
            } catch (error) {
                console.error('Error searching articles:', error);
                this.searchResults = [];
            }
        },
        openFullArticle() {
            if (this.selectedArticle) {
                // Abrir el artículo completo en una nueva ventana o modal
                window.open(`/help/${this.selectedArticle.slug}`, '_blank');
            }
        }
    }
}
</script>

<style scoped>
/* Animaciones y transiciones */
.fixed {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

/* Scrollbar personalizado */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
