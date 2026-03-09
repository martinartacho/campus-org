@extends('campus.shared.layout')

@section('title', __('Exemple de Colors del Sistema Manager'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('Exemple de Colors del Sistema Manager') }}</h1>
        <p class="text-gray-600">{{ __('Exemples pràctics d\'ús dels colors personalitzats per als rols del sistema manager') }}</p>
    </div>

    {{-- Manager - Purple --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">{{ __('Badges de Rols') }}</h2>
        <div class="flex flex-wrap gap-2 mb-6">
            <span class="bg-manager-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                Manager
            </span>
            
            <span class="bg-comunicacio-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Comunicació
            </span>
            
            <span class="bg-coordinacio-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Coordinació
            </span>
            
            <span class="bg-gestio-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Gestió
            </span>
        </div>
    </div>

    {{-- Componentes --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">{{ __('Component Campus Button') }}</h2>
        <div class="flex flex-wrap gap-2 mb-6">
            <button class="bg-manager-600 hover:bg-manager-700 text-white px-4 py-2 rounded text-sm">
                Accés Manager
            </button>
            
            <button class="bg-comunicacio-500 hover:bg-comunicacio-600 text-white px-4 py-2 rounded text-sm">
                Comunicació
            </button>
            
            <button class="bg-coordinacio-500 hover:bg-coordinacio-600 text-white px-4 py-2 rounded text-sm">
                Coordinació
            </button>
            
            <button class="bg-gestio-500 hover:bg-gestio-600 text-white px-4 py-2 rounded text-sm">
                Gestió
            </button>
        </div>
    </div>

    {{-- Ejemplos de código --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">{{ __('Exemples de Codi') }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Badges --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-md font-medium text-gray-800 mb-3">{{ __('Badges') }}</h3>
                <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto"><code>&lt;!-- Badges de rols --&gt;
&lt;span class="bg-manager-600 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Manager
&lt;/span&gt;

&lt;span class="bg-comunicacio-500 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Comunicació
&lt;/span&gt;

&lt;span class="bg-coordinacio-500 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Coordinació
&lt;/span&gt;

&lt;span class="bg-gestio-500 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Gestió
&lt;/span&gt;</code></pre>
            </div>

            {{-- Botones --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-md font-medium text-gray-800 mb-3">{{ __('Botons') }}</h3>
                <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto"><code>&lt;!-- Botons amb variants --&gt;
&lt;x-campus-button variant="manager" href="#"&gt;
    Accés Manager
&lt;/x-campus-button&gt;

&lt;x-campus-button variant="comunicacio" href="#"&gt;
    Comunicació
&lt;/x-campus-button&gt;

&lt;x-campus-button variant="coordinacio" href="#"&gt;
    Coordinació
&lt;/x-campus-button&gt;

&lt;x-campus-button variant="gestio" href="#"&gt;
    Gestió
&lt;/x-campus-button&gt;</code></pre>
            </div>
        </div>
    </div>

    {{-- Estados interactivos --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">{{ __('Estats Interactius') }}</h2>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 mb-4">{{ __('Els colors inclouen estats hover, focus i active automàtics:') }}</p>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li><strong>Hover:</strong> <code class="bg-gray-100 px-2 py-1 rounded">hover:bg-{color}-{shade}</code></li>
                <li><strong>Focus:</strong> <code class="bg-gray-100 px-2 py-1 rounded">focus:ring-{color}</code></li>
                <li><strong>Active:</strong> <code class="bg-gray-100 px-2 py-1 rounded">active:bg-{color}-{shade}</code></li>
            </ul>
        </div>
    </div>

    {{-- Referencia --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-2">
            <i class="bi bi-info-circle mr-2"></i>{{ __('Referència Ràpida') }}
        </h3>
        <div class="text-blue-700">
            <p class="mb-2">{{ __('Per a més informació, consulta els articles d\'ajuda:') }}</p>
            <ul class="list-disc list-inside space-y-1">
                <li><a href="/campus/help/articles/colors-sistema-manager" class="text-blue-600 hover:underline">{{ __('Colors del Sistema Manager') }}</a></li>
                <li><a href="/campus/help/articles/us-colors-badges" class="text-blue-600 hover:underline">{{ __('Ús de Colors en Badges') }}</a></li>
                <li><a href="/campus/help/articles/component-campus-button" class="text-blue-600 hover:underline">{{ __('Component Campus Button') }}</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection
