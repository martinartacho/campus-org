@auth
    @php
        $user = Auth::user();
        $isDesktop = isset($desktop) ? $desktop : true; // Per defecte desktop
        
        $hasCampusAccess = $user->hasAnyRole(['student', 'teacher']) || 
                          $user->canany(['campus.profile.view', 'campus.my_courses.view', 
                                        'campus.my_courses.manage', 'campus.courses.view']);
    @endphp
    
    @if($hasCampusAccess)
        {{-- VERSIÓ DESKTOP --}}
        @if($isDesktop)
        <div class="hidden sm:flex sm:items-center sm:ms-4">
            <x-dropdown align="right" width="56">
                <x-slot name="trigger">
                    <span class="inline-flex rounded-md">
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                            <i class="bi bi-mortarboard me-1"></i>
                            {{ __('campus.campus') }}
                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                            </svg>
                        </button>
                    </span>
                </x-slot>

                <x-slot name="content">
                    {{-- Perfil del campus --}}
                    @if($user->can('campus.profile.view'))
                        <x-dropdown-link :href="route('campus.profile')" :active="request()->routeIs('campus.profile')">
                            <div class="flex items-center">
                                <i class="bi bi-person-circle me-2"></i>
                                {{ __('El meu perfil') }}
                            </div>
                        </x-dropdown-link>
                    @endif
                    
                    {{-- Per a estudiants --}}
                    @if($user->hasRole('student') || $user->can('campus.my_courses.view'))
                        <x-dropdown-link :href="route('campus.my-courses')" :active="request()->routeIs('campus.my-courses.*')">
                            <div class="flex items-center">
                                <i class="bi bi-book me-2"></i>
                                {{ __('Els meus cursos') }}
                            </div>
                        </x-dropdown-link>
                        
                        <x-dropdown-link :href="route('campus.my-registrations')" :active="request()->routeIs('campus.my-registrations')">
                            <div class="flex items-center">
                                <i class="bi bi-clipboard-check me-2"></i>
                                {{ __('Les meves matriculacions') }}
                            </div>
                        </x-dropdown-link>
                    @endif
                    
                    {{-- Per a professors --}}
                    @if($user->hasRole('teacher') || $user->can('campus.my_courses.manage'))
                        <x-dropdown-link :href="route('campus.teacher-courses')" :active="request()->routeIs('campus.teacher-courses.*')">
                            <div class="flex items-center">
                                <i class="bi bi-person-chalkboard me-2"></i>
                                {{ __('Cursos que imparteixo') }}
                            </div>
                        </x-dropdown-link>
                        
                        <x-dropdown-link :href="route('campus.teacher-students')" :active="request()->routeIs('campus.teacher-students')">
                            <div class="flex items-center">
                                <i class="bi bi-people me-2"></i>
                                {{ __('Els meus estudiants') }}
                            </div>
                        </x-dropdown-link>
                    @endif
                    
                    {{-- Catàleg (per a tothom excepte admin) --}}
                    @if($user->can('campus.courses.view') && !$user->hasRole('admin'))
                        <x-dropdown-link :href="route('campus.catalog')" :active="request()->routeIs('campus.catalog')">
                            <div class="flex items-center">
                                <i class="bi bi-search me-2"></i>
                                {{ __('Catàleg de cursos') }}
                            </div>
                        </x-dropdown-link>
                        
                        <x-dropdown-link :href="route('campus.enroll')" :active="request()->routeIs('campus.enroll')">
                            <div class="flex items-center">
                                <i class="bi bi-clipboard-plus me-2"></i>
                                {{ __('Matricular-me') }}
                            </div>
                        </x-dropdown-link>
                    
                        @endif
                    
                        @if (Auth::user()->can('campus.categories.view')) 
                            <x-dropdown-link :href="route('campus.categories.index')" :active="request()->routeIs('admin.*')">
                                <div class="flex items-center">
                                    <i class="bi bi-gear me-2"></i>
                                    {{ __('Categories') }}
                                </div>
                            </x-dropdown-link>
                        @endif
                        
                        @if (Auth::user()->can('campus.seasons.view')) 
                            <x-dropdown-link :href="route('campus.seasons.index')" :active="request()->routeIs('admin.*')">
                                <div class="flex items-center">
                                    <i class="bi bi-gear me-2"></i>
                                    {{ __('Seasons') }}
                                </div>
                            </x-dropdown-link>
                        @endif
                </x-slot>
            </x-dropdown>
        </div>
        
        {{-- VERSIÓ MÒBIL --}}
        @else
        <div class="sm:hidden border-t border-gray-200 mt-2">
            <div class="px-4 py-2 text-gray-500 text-xs uppercase">
                <i class="bi bi-mortarboard me-1"></i>
                 {{ __('campus.campus') }}
            </div>
            
            {{-- Perfil del campus --}}
            @if($user->can('campus.profile.view'))
                <x-responsive-nav-link :href="route('campus.profile')" :active="request()->routeIs('campus.profile')">
                    <div class="flex items-center">
                        <i class="bi bi-person-circle me-2"></i>
                        {{ __('El meu perfil') }}
                    </div>
                </x-responsive-nav-link>
            @endif
            
            {{-- Per a estudiants --}}
            @if($user->hasRole('student') || $user->can('campus.my_courses.view'))
                <x-responsive-nav-link :href="route('campus.my-courses')" :active="request()->routeIs('campus.my-courses.*')">
                    <div class="flex items-center">
                        <i class="bi bi-book me-2"></i>
                        {{ __('Els meus cursos') }}
                    </div>
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('campus.my-registrations')" :active="request()->routeIs('campus.my-registrations')">
                    <div class="flex items-center">
                        <i class="bi bi-clipboard-check me-2"></i>
                        {{ __('Les meves matriculacions') }}
                    </div>
                </x-responsive-nav-link>
            @endif
            
            {{-- Per a professors --}}
            @if($user->hasRole('teacher') || $user->can('campus.my_courses.manage'))
                <x-responsive-nav-link :href="route('campus.teacher-courses')" :active="request()->routeIs('campus.teacher-courses.*')">
                    <div class="flex items-center">
                        <i class="bi bi-person-chalkboard me-2"></i>
                        {{ __('Cursos que imparteixo') }}
                    </div>
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('campus.teacher-students')" :active="request()->routeIs('campus.teacher-students')">
                    <div class="flex items-center">
                        <i class="bi bi-people me-2"></i>
                        {{ __('Els meus estudiants') }}
                    </div>
                </x-responsive-nav-link>
            @endif
            
            {{-- Catàleg (per a tothom excepte admin) --}}
            @if($user->can('campus.courses.view') && !$user->hasRole('admin'))
                <x-responsive-nav-link :href="route('campus.catalog')" :active="request()->routeIs('campus.catalog')">
                    <div class="flex items-center">
                        <i class="bi bi-search me-2"></i>
                        {{ __('Catàleg de cursos') }}
                    </div>
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('campus.enroll')" :active="request()->routeIs('campus.enroll')">
                    <div class="flex items-center">
                        <i class="bi bi-clipboard-plus me-2"></i>
                        {{ __('Matricular-me') }}
                    </div>
                </x-responsive-nav-link>
            @endif
        </div>
        @endif
    @endif
@endauth