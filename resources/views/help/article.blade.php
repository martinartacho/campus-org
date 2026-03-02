@extends('campus.shared.layout')

@section('title', __('campus.help_center') . ' - ' . $article->title)

@section('subtitle', __('campus.help_article'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
        <a href="{{ url('/') }}" class="hover:text-blue-600">
            <i class="bi bi-house mr-1"></i>
            {{ __('campus.home') }}
        </a>
        <span>/</span>
        <a href="{{ url('/help') }}" class="hover:text-blue-600">
            {{ __('campus.help') }}
        </a>
        <span>/</span>
        <span class="text-gray-900">{{ $article->title }}</span>
    </nav>

    <!-- Article Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $article->title }}</h1>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="bi bi-book mr-1"></i>
                        {{ ucfirst($article->area) }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ ucfirst($article->type) }}
                    </span>
                    <span class="text-gray-500">
                        <i class="bi bi-clock mr-1"></i>
                        {{ $article->updated_at->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-2">
                <button
                    onclick="window.print()"
                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                    title="{{ __('campus.print_article') }}"
                >
                    <i class="bi bi-printer"></i>
                </button>
                <button
                    onclick="copyLink()"
                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                    title="{{ __('campus.copy_link') }}"
                >
                    <i class="bi bi-link-45deg"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Article Content -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="prose max-w-none">
            <div class="text-gray-700 leading-relaxed">{!! nl2br($article->content) !!}</div>
        </div>

        <!-- Related Articles -->
        @if($relatedArticles->count() > 0)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('campus.related_articles') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($relatedArticles as $related)
                <a href="{{ url('/help/' . $related->slug) }}" class="block p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                    <h4 class="font-medium text-gray-800 mb-2">{{ $related->title }}</h4>
                    <p class="text-sm text-gray-600">{{ Str::limit($related->content, 100) }}...</p>
                    <div class="flex items-center mt-2 text-xs text-gray-500">
                        <i class="bi bi-book mr-1"></i>
                        {{ ucfirst($related->area) }}
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="flex space-x-4">
                    <button
                        onclick="markHelpful()"
                        class="flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
                    >
                        <i class="bi bi-hand-thumbs-up mr-2"></i>
                        {{ __('campus.useful') }}
                    </button>
                    <button
                        onclick="markNotHelpful()"
                        class="flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                    >
                        <i class="bi bi-hand-thumbs-down mr-2"></i>
                        {{ __('campus.not_useful') }}
                    </button>
                </div>
                <button
                    onclick="reportIssue()"
                    class="flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors"
                >
                    <i class="bi bi-flag mr-2"></i>
                    {{ __('campus.report_problem') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div id="feedback-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 id="feedback-title" class="text-lg font-semibold text-gray-900 mb-4">
            {{ __('campus.was_article_useful') }}
        </h3>
        <textarea
            id="feedback-text"
            rows="4"
            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="{{ __('campus.additional_comments') }}"
        ></textarea>
        <div class="flex justify-end space-x-3 mt-4">
            <button
                onclick="closeFeedback()"
                class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors"
            >
                {{ __('campus.cancel') }}
            </button>
            <button
                onclick="submitFeedback()"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
            >
                {{ __('campus.save') }}
            </button>
        </div>
    </div>
</div>

<script>
let feedbackType = null;

function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        showNotification('Enllaç copiat al porta-retalls', 'success');
    });
}

function markHelpful() {
    feedbackType = 'helpful';
    showFeedbackModal('T\'ha estat útil aquest article?', 'Comentaris addicionals (opcional)');
}

function markNotHelpful() {
    feedbackType = 'not_helpful';
    showFeedbackModal('Què podríem millorar?', 'Explica\'ns què podríem millorar...');
}

function reportIssue() {
    feedbackType = 'issue';
    showFeedbackModal('Informar d\'un problema', 'Descriu el problema que has trobat...');
}

function showFeedbackModal(title, placeholder) {
    document.getElementById('feedback-title').textContent = title;
    document.getElementById('feedback-text').placeholder = placeholder;
    document.getElementById('feedback-modal').style.display = 'flex';
}

function closeFeedback() {
    document.getElementById('feedback-modal').style.display = 'none';
    document.getElementById('feedback-text').value = '';
}

function submitFeedback() {
    const feedbackText = document.getElementById('feedback-text').value;
    
    // Enviar feedback a API
    fetch('/help/feedback', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            type: feedbackType,
            text: feedbackText,
            article: '{{ $article->id }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Gràcies pel teu feedback', 'success');
            closeFeedback();
        } else {
            showNotification('Error al enviar el feedback', 'error');
        }
    })
    .catch(error => {
        console.error('Error sending feedback:', error);
        showNotification('Error de connexió', 'error');
    });
}

function showNotification(message, type) {
    // Implementar notificación (podrías usar SweetAlert2)
    console.log(`${type}: ${message}`);
    Swal.fire({
        icon: type === 'success' ? 'success' : 'info',
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
}
</script>

<style>
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
@endsection
