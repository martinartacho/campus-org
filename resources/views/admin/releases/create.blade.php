@extends('campus.shared.layout')

@section('title', __('Crear Release'))

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <div class="sm:mx-auto sm:w-full sm:max-w-md">
                    <div class="bg-white px-4 py-8 shadow sm:rounded-lg sm:px-10">
                        <form class="space-y-6" action="{{ route('admin.releases.store') }}" method="POST">
                            @csrf
                            
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">{{ __('Títol') }}</label>
                                <div class="mt-1">
                                    <input id="title" name="title" type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                </div>
                            </div>

                            <div>
                                <label for="version" class="block text-sm font-medium text-gray-700">{{ __('Versió') }}</label>
                                <div class="mt-1">
                                    <input id="version" name="version" type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                </div>
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Tipus') }}</label>
                                <div class="mt-1">
                                    <select id="type" name="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        <option value="major">{{ __('Major') }}</option>
                                        <option value="minor">{{ __('Menor') }}</option>
                                        <option value="patch">{{ __('Patch') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="summary" class="block text-sm font-medium text-gray-700">{{ __('Resum') }}</label>
                                <div class="mt-1">
                                    <textarea id="summary" name="summary" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700">{{ __('Contingut') }}</label>
                                    <div class="mt-1">
                                        <textarea id="content" name="content" rows="10" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="bi bi-save mr-2"></i>
                                    {{ __('Crear Release') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        <i class="bi bi-info-circle text-blue-600 mr-2"></i>
                        {{ __('Ajuda per crear Releases') }}
                    </h3>
                    
                    <div class="prose prose-sm max-w-none">
                        <h4 class="text-md font-semibold text-gray-900 mb-2">{{ __('Tipus de Release') }}</h4>
                        <ul class="list-disc list-inside space-y-2 text-gray-600">
                            <li><strong>{{ __('Major') }}:</strong> Canvis importants que poden trencar la compatibilitat amb versions anteriors.</li>
                            <li><strong>{{ __('Menor') }}:</strong> Noves funcionalitats però compatibles amb versions anteriors.</li>
                            <li><strong>{{ __('Patch') }}:</strong> Correcció d'errors sense canvis funcionals.</li>
                        </ul>
                        
                        <h4 class="text-md font-semibold text-gray-900 mb-2 mt-4">{{ __('Format del Contingut') }}</h4>
                        <p class="text-gray-600 mb-2">Pots utilitzar <strong>Markdown</strong> per formatar el text:</p>
                        <ul class="list-disc list-inside space-y-1 text-gray-600 text-sm">
                            <li><code># Títol 1</code> - Títol principal</li>
                            <li><code>## Títol 2</code> - Subtítol</li>
                            <li><code>### Títol 3</code> - Sub-subtítol</li>
                            <li><code>*text en negreta*</code> - Text en negreta</li>
                            <li><code>_text en cursiva_</code> - Text en cursiva</li>
                            <li><code>`còdi inline`</code> - Còdi de programació</li>
                            <li><code>- Element de llista</code> - Llista</li>
                        </ul>
                        
                        <h4 class="text-md font-semibold text-gray-900 mb-2 mt-4">{{ __('Estat del Release') }}</h4>
                        <p class="text-gray-600">Els releases es creen com a <strong>esborranys</strong> i es poden editar. Un cop publicats, ja no es poden modificar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
