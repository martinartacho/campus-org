@extends('campus.shared.layout')
@section('title', __('Professorat'))
@section('subtitle', __('campusAccés a la zona de cobrament'))

@section('content')

<div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-blue-600 text-white px-6 py-4">
                <h3 class="text-xl font-semibold flex items-center">
                    <i class="bi bi-headset mr-2"></i>
                    Sol·licitud de Servei / Incidència
                </h3>
            </div>
            
            <div class="p-6">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('support.store') }}" id="supportForm">
                            @csrf

                            {{-- Información del usuario --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="bi bi-person mr-1"></i> Nom
                                    </label>
                                    <input type="text" 
                                        name="name" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                        value="{{ $user?->name ?? old('name') }}" 
                                        placeholder="El teu nom complet"
                                        required>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="bi bi-envelope mr-1"></i> Email
                                    </label>
                                    <input type="email" 
                                        name="email" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                        value="{{ $user?->email ?? old('email') }}" 
                                        placeholder="teu.email@exemple.com"
                                        required>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            @if($user?->department)
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-building me-1"></i> Departament
                                </label>
                                <input type="text" 
                                    name="department" 
                                    class="form-control"
                                    value="{{ $user->department->name ?? old('department') }}" 
                                    readonly
                                    class="form-control-plaintext">
                                <small class="text-muted">Departament detectat automàticament</small>
                            </div>
                            @endif

                            {{-- Tipo de solicitud --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-tag me-1"></i> Tipus de sol·licitud
                                </label>
                                <select name="type" 
                                        class="form-select @error('type') is-invalid @enderror"
                                        required>
                                    <option value="">Selecciona un tipus...</option>
                                    <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>
                                        🚀 Nou servei
                                    </option>
                                    <option value="incident" {{ old('type') == 'incident' ? 'selected' : '' }}>
                                        ⚠️ Incidència
                                    </option>
                                    <option value="improvement" {{ old('type') == 'improvement' ? 'selected' : '' }}>
                                        💡 Millora
                                    </option>
                                    <option value="consultation" {{ old('type') == 'consultation' ? 'selected' : '' }}>
                                        ❓ Consulta
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Descripción --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-text-left me-1"></i> Descripció
                                </label>
                                <textarea name="description" 
                                        class="form-control @error('description') is-invalid @enderror"
                                        rows="4" 
                                        placeholder="Descriu detallament la teva sol·licitud..."
                                        required>{{ old('description') }}</textarea>
                                <small class="text-muted">Mínim 10 caràcters</small>
                                @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Módulo y URL --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-window me-1"></i> Pantalla / Mòdul afectat
                                    </label>
                                    <input type="text" 
                                        name="module" 
                                        class="form-control"
                                        value="{{ $module ?? old('module') }}" 
                                        placeholder="Ex: Taulell de control, Cursos...">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-link-45deg me-1"></i> URL d'origen
                                    </label>
                                    <input type="url" 
                                        name="url" 
                                        class="form-control"
                                        value="{{ $url ?? old('url') }}" 
                                        readonly
                                        class="form-control-plaintext">
                                    <small class="text-muted">URL detectada automàticament</small>
                                </div>
                            </div>

                            {{-- Urgencia --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Nivell d'urgència
                                </label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                type="radio" 
                                                name="urgency" 
                                                value="low" 
                                                id="urgency-low"
                                                {{ old('urgency') == 'low' ? 'checked' : '' }}>
                                            <label class="form-check-label text-success" for="urgency-low">
                                                <i class="bi bi-arrow-down me-1"></i> Baixa
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                type="radio" 
                                                name="urgency" 
                                                value="medium" 
                                                id="urgency-medium"
                                                {{ old('urgency') == 'medium' ? 'checked' : 'checked' }}>
                                            <label class="form-check-label text-warning" for="urgency-medium">
                                                <i class="bi bi-dash me-1"></i> Mitjana
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                type="radio" 
                                                name="urgency" 
                                                value="high" 
                                                id="urgency-high"
                                                {{ old('urgency') == 'high' ? 'checked' : '' }}>
                                            <label class="form-check-label text-danger" for="urgency-high">
                                                <i class="bi bi-arrow-up me-1"></i> Alta
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                type="radio" 
                                                name="urgency" 
                                                value="critical" 
                                                id="urgency-critical"
                                                {{ old('urgency') == 'critical' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="urgency-critical">
                                                <i class="bi bi-exclamation-circle me-1"></i> Crítica
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Información de autenticación --}}
                            @if($user)
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Estàs autenticat com:</strong> {{ $user->name }} ({{ $user->email }})
                                <br>
                                <small>La teva sol·licitud quedarà associada al teu compte d'usuari.</small>
                            </div>
                            @else
                            <div class="alert alert-warning mb-4">
                                <i class="bi bi-person-x me-2"></i>
                                <strong>No estàs autenticat</strong>
                                <br>
                                <small>Pots enviar la sol·licitud de totes maneres. Si tens un compte, 
                                <a href="{{ route('login') }}" class="alert-link">inicia sessió</a> 
                                per obtenir un seguiment més personalitzat.</small>
                            </div>
                            @endif

                            {{-- Botones --}}
                            <div class="flex justify-between mt-6">
                                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                    <i class="bi bi-arrow-left mr-2"></i> Tornar
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" id="submitBtn">
                                    <i class="bi bi-send mr-2"></i> Enviar sol·licitud
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Información adicional --}}
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Informació addicional
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>⏰ Temps de resposta estimat:</h6>
                                <ul class="small mb-3">
                                    <li><strong>Baixa:</strong> 24-48h</li>
                                    <li><strong>Mitjana:</strong> 12-24h</li>
                                    <li><strong>Alta:</strong> 4-8h</li>
                                    <li><strong>Crítica:</strong> 1-2h</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>📧 Contacte directe:</h6>
                                <ul class="small mb-0">
                                    <li><strong>Email:</strong> support@upg.cat</li>
                                    <li><strong>Telèfon:</strong> 93 123 45 67</li>
                                    <li><strong>Horari:</strong> 9:00 - 18:00</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-detectar módulo desde URL si está vacío
    const moduleField = document.querySelector('input[name="module"]');
    const urlField = document.querySelector('input[name="url"]');
    
    if (moduleField && urlField && !moduleField.value && urlField.value) {
        const path = new URL(urlField.value).pathname;
        const moduleMap = {
            '/dashboard': 'Taulell de control',
            '/campus': 'Campus',
            '/courses': 'Cursos',
            '/teachers': 'Professors',
            '/treasury': 'Tresoreria',
            '/admin': 'Administració',
            '/profile': 'Perfil'
        };
        
        for (const [route, moduleName] of Object.entries(moduleMap)) {
            if (path.startsWith(route)) {
                moduleField.value = moduleName;
                break;
            }
        }
    }
    
    // Validación del formulario antes de enviar
    const form = document.getElementById('supportForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviant...';
        
        // El formulario se enviará normalmente
    });
});
</script>
@endpush
