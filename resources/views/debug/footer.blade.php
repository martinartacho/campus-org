@auth
    @php
        $user = Auth::user();
        $currentRoute = Route::currentRouteName();
        $currentAction = Route::currentRouteAction();
        $currentUrl = url()->current();
        $routeParameters = Route::current()->parameters();
        
        // Determinar contexto
        $isAdminArea = strpos($currentUrl, '/admin') !== false;
        $isApiArea = strpos($currentUrl, '/api') !== false;
        $isDashboard = $currentRoute === 'dashboard';
        
        // Obtener información del usuario
        $userRoles = $user->getRoleNames();
        $userPermissions = $user->getAllPermissions()->pluck('name');
        $directPermissions = $user->getDirectPermissions()->pluck('name');
        
        // Determinar tipo de usuario para CRM
        $userType = 'desconocido';
        if ($user->hasRole('admin') || $user->hasRole('superadmin')) {
            $userType = 'administrador';
        } elseif ($user->hasRole('director')) {
            $userType = 'director';
        } elseif ($user->hasRole('profesor')) {
            $userType = 'profesor';
        } elseif ($user->hasRole('secretaria')) {
            $userType = 'secretaría';
        } elseif ($user->hasRole('alumno')) {
            $userType = 'alumno';
        }
        
        // Verificar permisos para la ruta actual
        $routePermissions = [];
        if ($currentRoute) {
            // Mapeo de rutas a permisos necesarios (personalizar según tu app)
            $routePermissionMap = [
                'admin.users.index' => 'users.view',
                'admin.users.create' => 'users.create',
                'admin.users.edit' => 'users.edit',
                'cursos.index' => 'cursos.view',
                'cursos.create' => 'cursos.create',
                'matriculas.create' => 'matriculas.create',
                // Agrega más mapeos según necesites
            ];
            
            if (isset($routePermissionMap[$currentRoute])) {
                $routePermissions[] = $routePermissionMap[$currentRoute];
            }
        }
        
        // Verificar acceso a la ruta actual
        $hasRouteAccess = true;
        foreach ($routePermissions as $perm) {
            if (!$user->can($perm)) {
                $hasRouteAccess = false;
                break;
            }
        }
    @endphp

    @if(config('app.debug') || $user->hasRole('admin'))
        <div >
            <div class="container mx-auto px-4 py-2">
                <!-- Header -->
                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center space-x-4">
                        <span class="px-2 py-1 bg-blue-600 rounded">DEBUG CRM</span>
                        <span class="text-gray-300">{{ now()->format('H:i:s') }}</span>
                        <span class="px-2 py-1 bg-purple-600 rounded">{{ $userType }}</span>
                    </div>
                    <button onclick="toggleDebugDetails()" class="px-2 py-1 bg-gray-700 hover:bg-gray-600 rounded">
                        <span id="debugToggleText">▼ Mostrar detalles</span>
                    </button>
                </div>
                
                <!-- Quick Info -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-2">
                    <div class="bg-gray-800 p-2 rounded">
                        <div class="text-gray-400">Usuario</div>
                        <div class="font-semibold">{{ $user->name }}</div>
                        <div class="text-gray-300 text-xs">{{ $user->email }}</div>
                    </div>
                    
                    <div class="bg-gray-800 p-2 rounded">
                        <div class="text-gray-400">Roles</div>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($userRoles as $role)
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-red-500',
                                        'superadmin' => 'bg-red-700',
                                        'director' => 'bg-blue-500',
                                        'profesor' => 'bg-green-500',
                                        'secretaria' => 'bg-purple-500',
                                        'alumno' => 'bg-yellow-500',
                                        'gestor' => 'bg-indigo-500',
                                        'treasury' => 'bg-pink-500',
                                        'editor' => 'bg-teal-500',
                                        'user' => 'bg-gray-500',
                                    ];
                                    $color = $roleColors[$role] ?? 'bg-gray-600';
                                @endphp
                                <span class="px-2 py-0.5 {{ $color }} rounded text-xs">{{ $role }}</span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="bg-gray-800 p-2 rounded">
                        <div class="text-gray-400">Ruta Actual</div>
                        <div class="truncate font-semibold">{{ $currentRoute ?? 'N/A' }}</div>
                        <div class="text-gray-300 text-xs truncate">{{ $currentUrl }}</div>
                    </div>
                    
                    <div class="bg-gray-800 p-2 rounded">
                        <div class="text-gray-400">Acceso Ruta</div>
                        <div class="mt-1">
                            @if($hasRouteAccess)
                                <span class="px-2 py-0.5 bg-green-500 rounded text-xs">PERMITIDO</span>
                            @elseif(empty($routePermissions))
                                <span class="px-2 py-0.5 bg-yellow-500 rounded text-xs">NO VERIFICADO</span>
                            @else
                                <span class="px-2 py-0.5 bg-red-500 rounded text-xs">DENEGADO</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Detailed Info (Collapsed by default) -->
                <div id="debugDetails" class="hidden space-y-3 mt-3 pt-3 border-t border-gray-700">
                    <!-- Permissions Section -->
                    <div>
                        <div class="text-gray-400 mb-1">Permisos del Usuario ({{ $userPermissions->count() }})</div>
                        <div class="flex flex-wrap gap-1">
                            @foreach($userPermissions->take(20) as $permission)
                                @php
                                    $permType = $directPermissions->contains($permission) ? 'directo' : 'rol';
                                    $color = $permType === 'directo' ? 'bg-blue-400' : 'bg-green-400';
                                @endphp
                                <span class="px-2 py-0.5 {{ $color }} rounded text-xs" title="{{ $permType }}">
                                    {{ $permission }}
                                </span>
                            @endforeach
                            @if($userPermissions->count() > 20)
                                <span class="px-2 py-0.5 bg-gray-600 rounded text-xs">
                                    +{{ $userPermissions->count() - 20 }} más
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Route Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <div class="text-gray-400 mb-1">Información de Ruta</div>
                            <div class="bg-gray-800 p-2 rounded text-xs">
                                <div><span class="text-gray-400">Acción:</span> {{ $currentAction }}</div>
                                @if(!empty($routeParameters))
                                    <div class="mt-1">
                                        <span class="text-gray-400">Parámetros:</span>
                                        @foreach($routeParameters as $key => $value)
                                            <span class="ml-2 px-1 bg-gray-700 rounded">{{ $key }}: {{ $value }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-gray-400 mb-1">Contexto CRM</div>
                            <div class="bg-gray-800 p-2 rounded text-xs">
                                <div class="flex items-center">
                                    <span class="text-gray-400">Área:</span>
                                    <span class="ml-2 px-2 py-0.5 
                                        {{ $isAdminArea ? 'bg-red-400' : ($isApiArea ? 'bg-purple-400' : 'bg-blue-400') }} 
                                        rounded">
                                        {{ $isAdminArea ? 'Administración' : ($isApiArea ? 'API' : 'Frontend') }}
                                    </span>
                                </div>
                                <div class="mt-1">
                                    <span class="text-gray-400">Permisos requeridos:</span>
                                    @if(!empty($routePermissions))
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            @foreach($routePermissions as $perm)
                                                @php
                                                    $hasPerm = $user->can($perm);
                                                    $color = $hasPerm ? 'bg-green-500' : 'bg-red-500';
                                                @endphp
                                                <span class="px-2 py-0.5 {{ $color }} rounded">
                                                    {{ $perm }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="ml-2 text-gray-300">Ninguno específico</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('profile.edit') }}" 
                      {{--  <a href="#" --}}
                           class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-xs">
                            👤 Mi Perfil
                        </a>
                        @if($user->hasRole('admin'))
                        {{--     <a href="{{ route('admin.users.index') }}"  --}}
                        <a href="#"
                               class="px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-xs">
                                👑 Admin Users
                            </a>

                            {{-- <a href="{{ route('roles.index') ?? '#' }}"  --}}
                            <a href="#"
                               class="px-3 py-1 bg-purple-600 hover:bg-purple-700 rounded text-xs">
                                🔑 Roles
                            </a>
                        @endif
                        @if($user->hasRole(['profesor', 'director']))
                            {{-- <a href="{{ route('cursos.index') ?? '#' }}"  --}}
                            <a href="#"
                               class="px-3 py-1 bg-green-600 hover:bg-green-700 rounded text-xs">
                                📚 Cursos
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleDebugDetails() {
                const details = document.getElementById('debugDetails');
                const toggleText = document.getElementById('debugToggleText');
                
                if (details.classList.contains('hidden')) {
                    details.classList.remove('hidden');
                    toggleText.textContent = '▲ Ocultar detalles';
                } else {
                    details.classList.add('hidden');
                    toggleText.textContent = '▼ Mostrar detalles';
                }
            }
            
            // Auto-refresh cada 30 segundos (opcional)
            // setTimeout(() => {
            //    window.location.reload();
            // }, 30000);
        </script>
        
        <style>
            /* Animación sutil para cambios */
            .debug-highlight {
                animation: highlight 2s ease;
            }
            
            @keyframes highlight {
                0% { background-color: rgba(59, 130, 246, 0.5); }
                100% { background-color: transparent; }
            }
        </style>
    @endif
@endauth