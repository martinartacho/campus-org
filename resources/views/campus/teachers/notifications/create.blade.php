@extends('campus.shared.layout')

@section('title', __('campus.create_notification'))
@section('subtitle', __('campus.teacher_notifications'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teachers.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.teachers') }}
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teachers.notifications.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.teacher_notifications') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.create_notification') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg border">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="bi bi-bell-fill me-2 text-blue-600"></i>
                {{ __('campus.create_notification') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                {{ __('campus.create_notification_description') }}
            </p>
        </div>

        <form id="notification-form" method="POST" action="{{ route('campus.teachers.notifications.store') }}" class="p-6 space-y-6">
            @csrf
            
            <!-- Informació bàsica -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('campus.notification_title') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           placeholder="{{ __('campus.notification_title_placeholder') }}"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('campus.notification_type') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="type" 
                            name="type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>
                            {{ __('campus.notification_info') }}
                        </option>
                        <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>
                            {{ __('campus.notification_warning') }}
                        </option>
                        <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>
                            {{ __('campus.notification_success') }}
                        </option>
                        <option value="error" {{ old('type') == 'error' ? 'selected' : '' }}>
                            {{ __('campus.notification_error') }}
                        </option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contingut -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.notification_content') }} <span class="text-red-500">*</span>
                </label>
                <textarea id="content" 
                          name="content" 
                          rows="6"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                          placeholder="{{ __('campus.notification_content_placeholder') }}"
                          required>{{ old('content') }}</textarea>
                <div class="mt-1 text-sm text-gray-500">
                    {{ __('campus.characters_remaining', ['count' => '<span id="char-count">2000</span>']) }}
                </div>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Destinataris -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.notification_recipients') }} <span class="text-red-500">*</span>
                </label>
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="radio" 
                               id="recipient_all" 
                               name="recipient_type" 
                               value="all" 
                               {{ old('recipient_type') != 'filtered' && old('recipient_type') != 'specific' ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label for="recipient_all" class="ml-2 block text-sm text-gray-700">
                            {{ __('campus.notification_all_teachers') }}
                            <span class="text-gray-500">({{ $stats['total'] }} {{ __('campus.teachers_total') }})</span>
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="radio" 
                               id="recipient_filtered" 
                               name="recipient_type" 
                               value="filtered" 
                               {{ old('recipient_type') == 'filtered' ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label for="recipient_filtered" class="ml-2 block text-sm text-gray-700">
                            {{ __('campus.notification_filtered_teachers') }}
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="radio" 
                               id="recipient_specific" 
                               name="recipient_type" 
                               value="specific" 
                               {{ old('recipient_type') == 'specific' ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label for="recipient_specific" class="ml-2 block text-sm text-gray-700">
                            {{ __('campus.notification_specific_teachers') }}
                        </label>
                    </div>
                </div>

                @error('recipient_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Filtres (per a destinataris filtrats) -->
            <div id="filtered-recipients" class="{{ old('recipient_type') != 'filtered' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.notification_filters') }}
                </label>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-md">
                    <!-- Filtrar per IBAN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('campus.filter_by_iban') }}
                        </label>
                        <select name="filters[iban]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('campus.all') }}</option>
                            <option value="with" {{ old('filters.iban') == 'with' ? 'selected' : '' }}>
                                {{ __('campus.with_iban') }} ({{ $stats['with_iban'] }})
                            </option>
                            <option value="without" {{ old('filters.iban') == 'without' ? 'selected' : '' }}>
                                {{ __('campus.without_iban') }} ({{ $stats['without_iban'] }})
                            </option>
                        </select>
                    </div>

                    <!-- Filtrar per PDFs -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('campus.filter_by_pdfs') }}
                        </label>
                        <select name="filters[pdfs]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('campus.all') }}</option>
                            <option value="with" {{ old('filters.pdfs') == 'with' ? 'selected' : '' }}>
                                {{ __('campus.with_pdfs') }} ({{ $stats['with_pdfs'] }})
                            </option>
                            <option value="without" {{ old('filters.pdfs') == 'without' ? 'selected' : '' }}>
                                {{ __('campus.without_pdfs') }} ({{ $stats['without_pdfs'] }})
                            </option>
                        </select>
                    </div>

                    <!-- Filtrar per tipus de pagament -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('campus.filter_by_payment_type') }}
                        </label>
                        <div class="space-y-2">
                            @foreach(['ceded' => __('campus.payment_ceded'), 'own' => __('campus.payment_own'), 'waived' => __('campus.payment_waived')] as $type => $label)
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           name="filters[payment_types][]" 
                                           value="{{ $type }}" 
                                           {{ in_array($type, old('filters.payment_types', [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label class="ml-2 text-sm text-gray-700">
                                        {{ $label }} ({{ $stats['by_payment_type'][$type] ?? 0 }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selecció específica de professors -->
            <div id="specific-recipients" class="{{ old('recipient_type') != 'specific' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.select_teachers') }}
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-3 border border-gray-200 rounded-md">
                    @foreach($teachers as $teacher)
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="teacher_{{ $teacher->id }}" 
                                   name="recipient_ids[]" 
                                   value="{{ $teacher->user_id }}"
                                   {{ in_array($teacher->user_id, old('recipient_ids', [])) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="teacher_{{ $teacher->id }}" class="ml-2 block text-sm text-gray-700">
                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                                <span class="text-gray-500">({{ $teacher->user->email }})</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('recipient_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Opcions d'enviament -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.notification_options') }}
                </label>
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="send_immediately" 
                           name="send_immediately" 
                           value="1"
                           {{ old('send_immediately') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="send_immediately" class="ml-2 block text-sm text-gray-700">
                        {{ __('campus.send_immediately') }}
                    </label>
                </div>
            </div>

            <!-- Botons -->
            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('campus.teachers.notifications.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('campus.cancel') }}
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="bi bi-send-fill me-2"></i>
                    {{ __('campus.create_notification') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestió del tipus de destinataris
    const recipientRadios = document.querySelectorAll('input[name="recipient_type"]');
    const filteredSection = document.getElementById('filtered-recipients');
    const specificSection = document.getElementById('specific-recipients');

    recipientRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'filtered') {
                filteredSection.classList.remove('hidden');
                specificSection.classList.add('hidden');
            } else if (this.value === 'specific') {
                filteredSection.classList.add('hidden');
                specificSection.classList.remove('hidden');
            } else {
                filteredSection.classList.add('hidden');
                specificSection.classList.add('hidden');
            }
        });
    });

    // Comptador de caràcters
    const contentTextarea = document.getElementById('content');
    const charCount = document.getElementById('char-count');

    contentTextarea.addEventListener('input', function() {
        const remaining = 2000 - this.value.length;
        charCount.textContent = remaining;
        
        if (remaining < 100) {
            charCount.classList.add('text-red-600');
        } else {
            charCount.classList.remove('text-red-600');
        }
    });

    // Enviament del formulari via AJAX
    const form = document.getElementById('notification-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> {{ __("campus.sending") }}...';
        
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("campus.teachers.notifications.index") }}';
            } else {
                alert(data.message);
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("campus.error_sending_notification") }}');
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });
});
</script>
@endsection
