@extends('campus.shared.layout')

@section('title', __('campus.create_notification'))
@section('subtitle', $course->title)

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teacher.courses.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                @lang('campus.my_courses')
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teacher.courses.students', $course->id) }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.enrolled_students') }}
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
                {{ __('campus.create_notification') }} - {{ $course->code }}
            </h2>
        </div>

        <form method="POST" action="{{ route('campus.teacher.notifications.store', $course->id) }}" class="p-6 space-y-6">
            @csrf
            
            {{-- Información básica --}}
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
                        @foreach(\App\Models\TeacherNotification::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Contenido --}}
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
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Destinatarios --}}
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
                               {{ old('recipient_type') != 'specific' ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label for="recipient_all" class="ml-2 block text-sm text-gray-700">
                            {{ __('campus.notification_all_students') }}
                            <span class="text-gray-500">({{ $students->count() }} {{ __('campus.students_total') }})</span>
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
                            {{ __('campus.notification_specific_students') }}
                        </label>
                    </div>
                </div>

                @error('recipient_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Selección específica de estudiantes --}}
            <div id="specific-recipients" class="{{ old('recipient_type') != 'specific' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.select_students') }}
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-3 border border-gray-200 rounded-md">
                    @foreach($students as $student)
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="student_{{ $student->id }}" 
                                   name="recipient_ids[]" 
                                   value="{{ $student->user_id }}"
                                   {{ in_array($student->user_id, old('recipient_ids', [])) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="student_{{ $student->id }}" class="ml-2 block text-sm text-gray-700">
                                {{ $student->first_name }} {{ $student->last_name }}
                                <span class="text-gray-500">({{ $student->student_code }})</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('recipient_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Opciones de envío --}}
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

            {{-- Botones --}}
            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('campus.teacher.courses.students', $course->id) }}" 
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
    const recipientTypeRadios = document.querySelectorAll('input[name="recipient_type"]');
    const specificRecipients = document.getElementById('specific-recipients');

    function toggleSpecificRecipients() {
        if (document.querySelector('input[name="recipient_type"]:checked').value === 'specific') {
            specificRecipients.classList.remove('hidden');
        } else {
            specificRecipients.classList.add('hidden');
        }
    }

    recipientTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleSpecificRecipients);
    });

    // Estado inicial
    toggleSpecificRecipients();
});
</script>
@endsection
