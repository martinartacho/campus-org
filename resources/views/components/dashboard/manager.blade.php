@props([
    'stats' => [],
    'debug' => null,
    'error' => null,
])

<div class="bg-white p-6 rounded shadow">
    <h3 class="text-lg font-semibold mb-4">
        {{--  ({{ __('Equip Tècnic ') }}) conegut pel sobre nom d'<strong>ET</strong>  --}}
       {{ __('campus.campus_manager') }}
    </h3>

    
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-medium text-blue-800">{{ __('Estadístiques') }}</h4>
            
            
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @isset($stats['courses'])
                <span class="text-blue-700">{{ __('campus.courses') }}: {{ $stats['courses'] }}</span>
            @endisset

            @isset($stats['teachers'])
                <span class="text-blue-700">{{ __('campus.teachers') }}: {{ $stats['teachers'] }} </span>
            @endisset
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @isset($stats['students'])
                <span class="text-blue-700">{{ __('campus.students') }}: {{ $stats['students'] }}</span>
            @endisset

            @isset($stats['registrations'])
                <span class="text-blue-700">{{ __('campus.registrations') }}: {{ $stats['registrations'] }} </span>
            @endisset
        </div>
    </div>
</div> 

    <hr class="my-4">
<div class="bg-white p-6 rounded shadow">
    <h3 class="text-lg font-semibold mb-4">
       {{ __('Exemple de document') }}
    </h3>
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300">
        <div class="flex items-center justify-between">
        
        <article class="help-article">

        <!-- Capçalera -->
        <header class="help-header">
            <section> 
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Títol curt i accionable') }} *</label>
                <textarea
                    name="name"
                    id="name"
                    rows="1"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >Com publicar un material multimèdia
                </textarea>
            </div>
            </section>
            <section>
            <div class="help-meta">
            <h3>Etiquetes</h3>
            <textarea
                    name="name"
                    id="name"
                    rows="3"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >-- Àrea:Materials.
-- Context:Cursos → Materials.
-- Rols:Admin, Manager, Editor.
-- Tipus:Procediment
</textarea>

            <span><strong>Àrea:</strong> Materials</span>
            <span><strong>Context:</strong> Cursos → Materials</span>
            <span><strong>Rols:</strong> Admin, Manager, Editor</span>
            <span><strong>Tipus:</strong> Procediment</span>
            </div>
        </header>
    </section>
        
  
        <hr>

        <!-- Context -->
        <section>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Context') }} *</label>
                <textarea
                    name="name"
                    id="name"
                    rows="3"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >Aquesta ajuda està disponible quan l’usuari es troba dins de la gestió de materials d’un curs.
                </textarea>

            </div>
        </section>

        <!-- Objectiu -->
        <section>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Objectiu') }} *</label>
                <textarea
                    name="name"
                    id="name"
                    rows="3"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >Explicar com publicar correctament un material multimèdia. (vídeo, àudio o document) perquè sigui visible per als alumnes.
                </textarea>
            </div>
            
        </section>

        <!-- Passos -->
        <section>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Passos') }} *</label>
                
                <textarea
                    name="name"
                    id="name"
                    rows="3"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
--Accedeix al curs corresponent des del panell de gestió.
-- Entra a l’apartat  Materials .
-- Fes clic a Afegir material.
-- Selecciona el tipus de material (vídeo, àudio o document).
-- Omple el títol i la descripció del material.
-- Carrega el fitxer o enllaça el contingut extern.
-- Marca l’opció "Publicat".
-- Desa els canvis.

                </textarea>
            </div>

            
        </section>

        <!-- Resultat esperat -->
        <section>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Resultat esperat') }} *</label>
                <textarea
                    name="name"
                    id="name"
                    rows="3"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >El material apareix visible dins del curs i pot ser consultat pels alumnes segons la seva matrícula..
                </textarea>
            </div>
            
        </section>

        <!-- Errors habituals -->
        <section>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Errors habituals') }} *</label>
                <textarea
                    name="name"
                    id="name"
                    rows="3"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >El material no es veu: comprova que estigui marcat com a "Publicat". Fitxer incorrecte: "revisa que el format sigui compatible".
                </textarea>
            </div>
        </section>

        <!-- Relacionats -->
        <section>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Relacionat') }} *</label>
                <textarea
                    name="name"
                    id="name"
                    rows="3"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >Estats dels materials. Com editar un material existent
                </textarea>
            </div>
            
        </section>

        <!-- Peu -->
        <footer class="help-footer">
            <p>
            <strong>Estat:</strong> Validat ·
            <strong>Versió:</strong> 1.0 ·
            <strong>Última revisió:</strong> 2026-02-06
            </p>
        </footer>

        </article>
    
    </div>
</div>


<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- CURSOS --}}
        @can('campus.courses.view')
            <a href="{{ route('manager.courses.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200 hover:border-green-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">{{ __('Cursos') }}</p>
                        <p class="text-2xl font-bold text-green-900">
                            @isset($stats['courses'])
                            {{ $stats['courses'] }}
                            @endisset
                        </p>
                    </div>
                    <div class="p-2 bg-green-200 rounded-lg">
                        <i class="bi bi-book text-green-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="mt-3 pt-2 border-t border-green-200">
                    <span class="text-xs text-green-600 hover:text-green-800 flex items-center">
                        Gestionar cursos <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
            </a>
        @endcan
        
        {{-- inscripcions --}}
        @can('campus.registrations.view')
            <a href="{{ route('manager.registrations.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-800">{{ __('Inscripcions') }}</p>
                        <p class="text-2xl font-bold text-blue-900">
                            @isset($stats['registrations'])
                            {{ $stats['registrations'] }}
                            @endisset
                        </p>
                    </div>
                    <div class="p-2 bg-blue-200 rounded-lg">
                        <i class="bi bi-people text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                        <span class="text-green-700">Actius: {{ $stats['active_courses'] ?? 0 }}</span>
                        <span class="text-green-700">Inactius: {{ $stats['inactive_courses'] ?? 0 }}</span>
                    </div>
                
                <div class="mt-3 pt-2 border-t border-blue-200">
                    <span class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                        Veure inscripcions <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
        @endcan


    </div>
</div>

