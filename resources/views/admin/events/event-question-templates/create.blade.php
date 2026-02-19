<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('site.Create Question Template') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.event-question-templates.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="template_name" class="block text-sm font-medium text-gray-700">
                                {{ __('site.Template Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="template_name" id="template_name" value="{{ old('template_name') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('template_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="template_description" class="block text-sm font-medium text-gray-700">
                                {{ __('site.Template Description') }}
                            </label>
                            <textarea name="template_description" id="template_description" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('template_description') }}</textarea>
                            @error('template_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="question" class="block text-sm font-medium text-gray-700">
                                {{ __('site.Question') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="question" id="question" value="{{ old('question') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('question')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                {{ __('site.Type') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>{{ __('site.Text') }}</option>
                                <option value="single" {{ old('type') == 'single' ? 'selected' : '' }}>{{ __('site.Single Choice') }}</option>
                                <option value="multiple" {{ old('type') == 'multiple' ? 'selected' : '' }}>{{ __('site.Multiple Choice') }}</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4" id="options-container" style="{{ in_array(old('type'), ['single', 'multiple']) ? 'block' : 'none' }};">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('site.Options') }} <span class="text-red-500">*</span>
                            </label>
                            <p class="text-sm text-gray-500 mb-2">{{ __('site.Add at least one option for choice questions') }}</p>
                            <div id="options-list" class="space-y-2 mb-2">
                            </div>
                            <button type="button" id="add-option" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('site.Add Option') }}
                            </button>
                            @error('options')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('options.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="required" id="required" value="1" {{ old('required') ? 'checked' : '' }}
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="required" class="ml-2 block text-sm text-gray-900">{{ __('site.Required') }}</label>
                            </div>
                            @error('required')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.event-question-templates.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                {{ __('site.Cancel') }}
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('site.Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const optionsContainer = document.getElementById('options-container');
        const optionsList = document.getElementById('options-list');
        const addOptionButton = document.getElementById('add-option');

        if (!typeSelect || !optionsContainer || !optionsList || !addOptionButton) {
            return;
        }

        function toggleOptions() {
            if (typeSelect.value === 'single' || typeSelect.value === 'multiple') {
                optionsContainer.style.display = 'block';
                if (optionsList.children.length === 0) {
                    addOption();
                }
            } else {
                optionsContainer.style.display = 'none';
                optionsList.innerHTML = '';
            }
        }

        function addOption(value = '') {
            const optionIndex = optionsList.children.length;
            const optionDiv = document.createElement('div');
            optionDiv.className = 'flex items-center mb-2';
            optionDiv.innerHTML = `
                <input type="text" name="options[${optionIndex}]" value="${value.toString().replace(/"/g, '&quot;')}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="{{ __('site.Option') }}" required>
                <button type="button" class="ml-2 text-red-600 hover:text-red-800 remove-option">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            optionsList.appendChild(optionDiv);

            const removeButton = optionDiv.querySelector('.remove-option');
            removeButton.addEventListener('click', function() {
                optionDiv.remove();
                reindexOptions();
            });
        }

        function reindexOptions() {
            const options = optionsList.querySelectorAll('input');
            options.forEach((input, index) => {
                input.name = `options[${index}]`;
            });
        }

        addOptionButton.addEventListener('click', addOption);
        typeSelect.addEventListener('change', toggleOptions);

        @if(old('options'))
            const oldOptions = {!! json_encode(old('options')) !!};
            oldOptions.forEach(option => addOption(option));
        @endif
        
        toggleOptions();
    });
</script>
</x-app-layout>