@auth
    @php
        $user = Auth::user();
        $isDesktop = isset($desktop) ? $desktop : true;
        
    @endphp
    @php
        // Determinar quins ítems mostrar segons permisos
        $adminItems = [];
        
        // Feedback
        if (Auth::user()->can('users.view')) {
            $adminItems[] = [
                'href' => route('admin.feedback.index'),
                'active' => request()->routeIs('admin.feedback.index.*'),
                'label' => __('site.Feedback'),
                'icon' => 'chat-left-text',
            ];
        }
        
        // Usuaris
        if (Auth::user()->can('users.view')) {
            $adminItems[] = [
                'href' => route('admin.users.index'),
                'active' => request()->routeIs('admin.users.*'),
                'label' => __('site.Users'),
                'icon' => 'people',
            ];
        }
        
        // Rols
        if (Auth::user()->can('roles.index')) {
            $adminItems[] = [
                'href' => route('admin.roles.index'),
                'active' => request()->routeIs('admin.roles.*'),
                'label' => __('site.Roles'),
                'icon' => 'person-badge',
            ];
        }
        
        // Permisos
        if (Auth::user()->can('permissions.index')) {
            $adminItems[] = [
                'href' => route('admin.permissions.index'),
                'active' => request()->routeIs('admin.permissions.*'),
                'label' => __('site.Permissions'),
                'icon' => 'key',
            ];
        }
        
        // Esdeveniments
        if (Auth::user()->canany(['events.view', 'event_types.view', 'event_questions.view', 'event_answers.view'])) {
            $adminItems[] = [
                'href' => route('admin.events.index'),
                'active' => request()->routeIs('admin.events.*'),
                'label' => __('site.Events'),
                'icon' => 'calendar-event',
            ];
        }
        
        // Configuració
        if (Auth::user()->can('settings.edit')) {
            $adminItems[] = [
                'href' => route('settings.edit'),
                'active' => request()->routeIs('settings.*'),
                'label' => __('Settings'),
                'icon' => 'sliders',
            ];
        }
        
        // Campus - Categories
        if (Auth::user()->can('campus.categories.view')) {
            $adminItems[] = [
                'href' => route('campus.categories.index'),
                'active' => request()->routeIs('campus.categories.*'),
                'label' => __('Categories'),
                'icon' => 'folder',
            ];
        }
        
        // Campus - Temporades
        if (Auth::user()->can('campus.seasons.view')) {
            $adminItems[] = [
                'href' => route('campus.seasons.index'),
                'active' => request()->routeIs('campus.seasons.*'),
                'label' => __('Temporades'),
                'icon' => 'calendar-range',
            ];
        }
        
        // Campus - Cursos
        if (Auth::user()->can('campus.courses.view')) {
            $adminItems[] = [
                'href' => route('campus.courses.index'),
                'active' => request()->routeIs('campus.courses.*'),
                'label' => __('Cursos'),
                'icon' => 'book',
            ];
        }
        
        // Campus - Estudiants
        if (Auth::user()->can('campus.students.view')) {
            $adminItems[] = [
                'href' => route('campus.students.index'),
                'active' => request()->routeIs('campus.students.*'),
                'label' => __('Estudiants'),
                'icon' => 'person-video',
            ];
        }
        
        // Campus - Professors
        if (Auth::user()->can('campus.teachers.view')) {
            $adminItems[] = [
                'href' => route('campus.teachers.index'),
                'active' => request()->routeIs('campus.teachers.*'),
                'label' => __('Professors'),
                'icon' => 'person-standing',
            ];
        }
        
        // Campus - Matriculacions
        if (Auth::user()->can('campus.registrations.view')) {
            $adminItems[] = [
                'href' => route('campus.registrations.index'),
                'active' => request()->routeIs('campus.registrations.*'),
                'label' => __('Matriculacions'),
                'icon' => 'clipboard-check',
            ];
        }
    @endphp
    
    @if(!empty($adminItems))
        {{-- Per a versió d'escriptori --}}
        <div class="hidden sm:flex sm:items-center">
            <x-dropdown align="right" width="56">
                <x-slot name="trigger">
                    <span class="inline-flex rounded-md">
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                            <i class="bi bi-gear me-1"></i>
                            {{ __('site.Admin') }}
                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                            </svg>
                        </button>
                    </span>
                </x-slot>

                <x-slot name="content">
                    @foreach($adminItems as $item)
                        <x-dropdown-link :href="$item['href']" :active="$item['active']">
                            <div class="flex items-center">
                                <i class="bi bi-{{ $item['icon'] }} me-2"></i>
                                {{ $item['label'] }}
                            </div>
                        </x-dropdown-link>
                    @endforeach
                </x-slot>
            </x-dropdown>
        </div>
        
        {{-- Per a versió mòbil --}}
        <div class="sm:hidden border-t border-gray-200 mt-2">
            <div class="px-4 py-2 text-gray-500 text-xs uppercase">
                <i class="bi bi-gear me-1"></i>
                {{ __('site.Admin') }}
            </div>
            @foreach($adminItems as $item)
                <x-responsive-nav-link :href="$item['href']" :active="$item['active']">
                    <div class="flex items-center">
                        <i class="bi bi-{{ $item['icon'] }} me-2"></i>
                        {{ $item['label'] }}
                    </div>
                </x-responsive-nav-link>
            @endforeach
        </div>
    @endif
@endauth