<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head profile="http://www.w3.org/2005/10/profile">
        <!-- Favicon HTML5 Standard -->
        <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('img/favicon.svg') }}">
        <meta charset="utf-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Campus UPG') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
        <!-- SweetAlert2 CSS -->
        <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">

            @php
                $language = cache('global_language');
                if (!$language) {
                    try {
                        $setting = \App\Models\Setting::where('key', 'language')->first();
                        $language = $setting ? $setting->value : config('app.locale');
                        cache(['global_language' => $language], now()->addDay());
                    } catch (\Exception $e) {
                        $language = config('app.locale');
                    }
                }
            @endphp

            @if(session('status') === 'language-conflict-resolved')
                <div class="fixed bottom-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center animate-fade-in-out">
                    <i class="bi bi-check-circle-fill mr-2"></i>
                    {{ __('site.ConflictResolved') }}
                </div>
                <script>
                    setTimeout(() => {
                        document.querySelector('.animate-fade-in-out').style.opacity = '0';
                        setTimeout(() => {
                            document.querySelector('.animate-fade-in-out').remove();
                        }, 10000);
                    }, 30000);
                </script>
            @endif

            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>

            <!-- Botó d'ajuda flotant minimalista -->
            <a href="{{ url('/help') }}" class="fixed bottom-6 left-6 bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110 z-50" target="_help">
                <i class="bi bi-question-lg text-xl"></i>
            </a>

           <!--  // @  include ('debug.footer') -->
            <!-- Footer con botón de soporte flotante -->
            @include('components.footer')

        </div>

        <!-- SweetAlert2 JS -->
        <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}"></script>
        
        <!-- jQuery (required for Summernote) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Summernote Editor CSS -->
        <link href="{{ asset('vendor/summernote/summernote.min.css') }}" rel="stylesheet">
        
        <!-- Summernote Editor JS -->
        <script src="{{ asset('vendor/summernote/summernote.min.js') }}"></script>
        

        
        <script>
            // Función global para mostrar loader
            window.showLoader = function(form) {
                Swal.fire({
                    title: 'Enviando notificaciones',
                    html: 'Por favor espera...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        
                        // Envía el formulario después de mostrar el loader
                        setTimeout(() => {
                            form.submit();
                        }, 100);
                    }
                });
            };
        </script>
        
        @stack('scripts')
    </body>
</html>
