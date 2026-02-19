<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configuración del sitio') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- Sección de información de idiomas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <!-- Idioma actual en uso -->
                    <x-dashboard.card title="{{ __('site.Current_Language') }}" color="green">
                        <i class="bi bi-translate"></i>
                        @switch(app()->getLocale())
                            @case('es') {{ __('site.Spanish') }} @break
                            @case('ca') {{ __('site.Catalonia') }} @break
                            @default  {{ __('site.English') }}
                        @endswitch
                    </x-dashboard.card>

                    <!-- Idioma global del sitio -->
                    <x-dashboard.card title="{{ __('site.Global_Site_Language') }}" color="blue">
                        <i class="bi bi-globe"></i>
                        @switch($settings['language'])
                            @case('es') {{ __('site.Spanish') }} @break
                            @case('ca') {{ __('site.Catalonia') }} @break
                            @default  {{ __('site.English') }}
                        @endswitch
                    </x-dashboard.card>

                    <!-- Idioma del usuario (si está configurado) -->
                    @if($userLanguage = auth()->user()->settings()->where('key', 'language')->value('value'))
                    <x-dashboard.card title="{{ __('site.User_Language') }}" color="purple">
                        <i class="bi bi-person"></i>
                        @switch($userLanguage)
                            @case('es') {{ __('site.Spanish') }} @break
                            @case('ca') {{ __('site.Catalonia') }} @break
                            @default  {{ __('site.English') }}
                        @endswitch
                    </x-dashboard.card>
                    @endif

                    <!-- Idioma por defecto de la aplicación -->
                    <x-dashboard.card title="{{ __('site.Default_App_Language') }}" color="yellow">
                        <i class="bi bi-gear"></i>
                        @switch(config('app.locale'))
                            @case('es') {{ __('site.Spanish') }} @break
                            @case('ca') {{ __('site.Catalonia') }} @break
                            @default  {{ __('site.English') }}
                        @endswitch
                    </x-dashboard.card>

                    <!-- Locale de la aplicación (.env) -->
                    <x-dashboard.card title="{{ __('site.ENV_Locale') }}" color="gray">
                        <i class="bi bi-file-earmark-code"></i>
                        {{ config('app.locale') }}
                    </x-dashboard.card>
                </div>

                <!-- Formulario para actualizar el logo -->
                <form method="POST" action="{{ route('settings.updateLogo') }}" enctype="multipart/form-data" class="mb-8">
                    @csrf
                    <div class="mb-4">
                        <label for="logo" class="block text-sm font-medium text-gray-700">
                            {{ __('Logo del sitio') }}
                        </label>
                        <input type="file" name="logo" id="logo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" accept="image/*">
                    </div>
                    <div class="mt-6">
                        <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                    </div>
                </form>

                <!-- Formulario para actualizar el idioma global -->
                <form method="POST" action="{{ route('settings.updateLanguage') }}" class="mb-8">
                    @csrf
                    @method('PUT')
                    <div class="col-span-1">
                        <x-input-label :value="__('site.Default Language')" />
                        <div class="mt-2 space-y-2">
                            @foreach([
                                'es' => __('site.Spanish'),
                                'ca' => __('site.Catalonia'),
                                'en' => __('site.English'),
                            ] as $code => $label)
                                <label class="flex items-center">
                                    <input type="radio" 
                                        name="language" 
                                        value="{{ $code }}"
                                        {{ $code == $settings['language'] ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-6">
                        <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                    </div>
                </form>

                <!-- Sección de Logs Push -->
                <div class="mt-10">
                    <h2 class="text-xl font-semibold mb-4">
                        {{ __('site.Push') }}
                    </h2>
                    <p class="text-gray-600 mb-4">
                        {{ __('site.Push log files') }}
                    </p>

                    @if($settings['pushLogs']->isEmpty())
                        <p>{{ __('site.No logs available') }}.</p>
                    @else
                        <ul>
                            @foreach($settings['pushLogs'] as $log)
                                <li class="flex items-center justify-between py-2">
                                    <span>{{ $log->getFilename() }}</span>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('push.logs.download', $log->getFilename()) }}" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            <i class="bi bi-download"></i> {{ __('site.Download') }}
                                        </a>
                                        <form method="POST" action="{{ route('push.logs.delete', $log->getFilename()) }}" onsubmit="return confirm('¿Eliminar este log?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                                <i class="bi bi-trash"></i> {{ __('site.Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>