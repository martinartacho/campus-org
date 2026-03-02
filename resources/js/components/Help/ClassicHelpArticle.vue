<template>
    <div class="max-w-4xl mx-auto p-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
            <a href="#" @click="$emit('back')" class="hover:text-blue-600">
                <i class="bi bi-arrow-left mr-1"></i>
                Volver a ayuda
            </a>
            <span>/</span>
            <span class="text-gray-900">{{ article.area }}</span>
        </nav>

        <!-- Article Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ article.title }}</h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i :class="areas[article.area]?.icon" class="mr-1"></i>
                            {{ areas[article.area]?.name }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ article.type }}
                        </span>
                        <span class="text-gray-500">
                            <i class="bi bi-clock mr-1"></i>
                            {{ article.created_at }}
                        </span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button
                        @click="printArticle"
                        class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                        title="Imprimir artículo"
                    >
                        <i class="bi bi-printer"></i>
                    </button>
                    <button
                        @click="copyLink"
                        class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                        title="Copiar enlace"
                    >
                        <i class="bi bi-link-45deg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Article Content -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="prose max-w-none">
                <div v-html="article.content" class="text-gray-700 leading-relaxed"></div>
            </div>

            <!-- Related Articles -->
            <div v-if="relatedArticles.length > 0" class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Artículos relacionados</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="related in relatedArticles"
                        :key="related.id"
                        @click="$emit('selectArticle', related)"
                        class="p-4 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors"
                    >
                        <h4 class="font-medium text-gray-800 mb-2">{{ related.title }}</h4>
                        <p class="text-sm text-gray-600">{{ related.content.substring(0, 100) }}...</p>
                        <div class="flex items-center mt-2 text-xs text-gray-500">
                            <i :class="areas[related.area]?.icon" class="mr-1"></i>
                            {{ areas[related.area]?.name }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="flex space-x-4">
                        <button
                            @click="markHelpful"
                            class="flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
                        >
                            <i class="bi bi-hand-thumbs-up mr-2"></i>
                            Útil
                        </button>
                        <button
                            @click="markNotHelpful"
                            class="flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                        >
                            <i class="bi bi-hand-thumbs-down mr-2"></i>
                            No útil
                        </button>
                    </div>
                    <button
                        @click="reportIssue"
                        class="flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors"
                    >
                        <i class="bi bi-flag mr-2"></i>
                        Reportar problema
                    </button>
                </div>
            </div>
        </div>

        <!-- Feedback Modal -->
        <div v-if="showFeedback" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ feedbackType === 'helpful' ? '¿Te fue útil este artículo?' : '¿Qué podríamos mejorar?' }}
                </h3>
                <textarea
                    v-model="feedbackText"
                    rows="4"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :placeholder="feedbackType === 'helpful' ? 'Comentarios adicionales (opcional)' : 'Cuéntanos qué podríamos mejorar...'"
                ></textarea>
                <div class="flex justify-end space-x-3 mt-4">
                    <button
                        @click="showFeedback = false"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="submitFeedback"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    >
                        Enviar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ClassicHelpArticle',
    props: {
        article: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            areas: {
                cursos: { name: 'Cursos', icon: 'bi-book' },
                matricula: { name: 'Matrícula', icon: 'bi-person-plus' },
                materiales: { name: 'Materiales', icon: 'bi-folder' },
                configuracion: { name: 'Configuración', icon: 'bi-gear' }
            },
            relatedArticles: [],
            showFeedback: false,
            feedbackType: null,
            feedbackText: ''
        }
    },
    mounted() {
        this.loadRelatedArticles();
    },
    methods: {
        async loadRelatedArticles() {
            try {
                const response = await fetch(`/api/help/area/${this.article.area}`);
                const data = await response.json();
                this.relatedArticles = data.articles.filter(a => a.id !== this.article.id).slice(0, 4);
            } catch (error) {
                console.error('Error loading related articles:', error);
            }
        },
        printArticle() {
            window.print();
        },
        copyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                // Mostrar notificación de éxito
                this.showNotification('Enlace copiado al portapapeles', 'success');
            });
        },
        markHelpful() {
            this.feedbackType = 'helpful';
            this.showFeedback = true;
        },
        markNotHelpful() {
            this.feedbackType = 'not-helpful';
            this.showFeedback = true;
        },
        reportIssue() {
            this.feedbackType = 'issue';
            this.showFeedback = true;
        },
        async submitFeedback() {
            try {
                // Aquí podrías enviar el feedback a una API
                console.log('Feedback submitted:', {
                    type: this.feedbackType,
                    text: this.feedbackText,
                    article: this.article.id
                });
                
                this.showNotification('Gracias por tu feedback', 'success');
                this.showFeedback = false;
                this.feedbackText = '';
            } catch (error) {
                console.error('Error submitting feedback:', error);
                this.showNotification('Error al enviar feedback', 'error');
            }
        },
        showNotification(message, type) {
            // Implementar notificación (podrías usar una librería como toastify)
            console.log(`${type}: ${message}`);
        }
    }
}
</script>

<style scoped>
.prose h1, .prose h2, .prose h3 {
    @apply text-gray-900 font-semibold;
}

.prose h1 { @apply text-2xl mb-4; }
.prose h2 { @apply text-xl mb-3; }
.prose h3 { @apply text-lg mb-2; }

.prose p {
    @apply mb-4 leading-relaxed;
}

.prose ul, .prose ol {
    @apply mb-4 pl-6;
}

.prose li {
    @apply mb-2;
}

.prose code {
    @apply bg-gray-100 px-2 py-1 rounded text-sm font-mono;
}

.prose pre {
    @apply bg-gray-100 p-4 rounded-lg overflow-x-auto mb-4;
}

.prose blockquote {
    @apply border-l-4 border-blue-500 pl-4 italic text-gray-600 mb-4;
}

@media print {
    .no-print {
        display: none !important;
    }
}
</style>
