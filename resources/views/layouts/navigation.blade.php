{{-- resources/views/layouts/navigation.blade.php (CORRECTE) --}}
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>
                {{-- Desktop section --}}
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('site.Dashboard') }}
                    </x-nav-link>
                </div>
            </div>
            @auth
                @canany(['users.view', 'roles.index', 'permissions.index', 'settings.edit'])
                    <!-- Admin Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <div class="relative ms-3">
                            <x-dropdown align="left" width="56">
                                <x-slot name="trigger">
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                            {{ __('site.Admin') }}
                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                            </svg>
                                        </button>
                                    </span>
                                </x-slot>

                                <x-slot name="content">
                                    @can('users.view')
                                        <x-dropdown-link :href="route('admin.feedback.index')" :active="request()->routeIs('admin.feedback.index.*')">
                                            {{ __('site.Feedback') }}
                                        </x-dropdown-link>
                                    @endcan
                                    @can('users.view')
                                        <x-dropdown-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                            {{ __('site.Users') }}
                                        </x-dropdown-link>
                                    @endcan
                                    @can('roles.index')
                                        <x-dropdown-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                                            {{ __('site.Roles') }}
                                        </x-dropdown-link>
                                    @endcan
                                    @can('permissions.index')
                                        <x-dropdown-link :href="route('admin.permissions.index')" :active="request()->routeIs('admin.permissions.*')">
                                            {{ __('site.Permissions') }}
                                        </x-dropdown-link>
                                    @endcan
                                    @canany(['events.view', 'event_types.view', 'event_questions.view', 'event_answers.view'])
                                    <x-dropdown-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                                        {{ __('site.Events') }}
                                    </x-dropdown-link>
                                    @endcanany
                                    @can('settings.edit')
                                        <x-dropdown-link :href="route('settings.edit')" :active="request()->routeIs('settings.*')">
                                            {{ __('site.Settings') }}
                                        </x-dropdown-link>
                                    @endcan

                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                @endcanany
            @endauth

            <!-- Icono notificaciones en escritorio -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <ul class="navbar-nav ms-auto">
                    @include('components.notification-bell') 
                </ul>
            </div>    

            <!-- Menús dinàmics per a permisos -->
            <div class="flex items-center space-x-4">
               
                {{-- Menú de Campus (DESKTOP) --}}
                {{-- @include('components.menu-campus', ['desktop' => true]) --}}
                
                {{-- Menú d'Admin (DESKTOP) --}}
            {{--     @include('components.menu-admin', ['desktop' => true]) --}}
                
                {{-- Menú d'Usuari (DESKTOP) --}}
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    {{-- ... codi del dropdown del perfil ... --}}
                    @include('components.menu-user', ['desktop' => true])
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (MÒBIL) -->
   {{-- Mobile section --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('site.Dashboard') }}
            </x-responsive-nav-link>

            {{-- Icono de notificaciones en móvil --}}
            <div class="border-t border-gray-200 mt-2">
                <ul class="px-4 py-2">
                    @include('components.notification-bell')
                </ul>
            </div>
         
            {{-- Menú de Campus (MÒBIL) --}}
           {{--  @include('components.menu-campus', ['desktop' => false]) --}}
            
            {{-- Menú d'Admin (MÒBIL) --}}
          {{--   @include('components.menu-admin', ['desktop' => false]) --}}
            
            {{-- Menú d'User (MÒBIL) --}}
            @include('components.menu-user', ['desktop' => false])

            
        </div>

        <!-- Responsive Settings Options -->
{{--             <div class="pt-2 pb-3 space-y-1">
            </div> --}}
    </div>
</nav>