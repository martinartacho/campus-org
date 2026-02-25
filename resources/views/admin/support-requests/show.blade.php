@extends('campus.shared.layout')

@section('title', 'Detall de Sol·licitud de Suport')
@section('subtitle', 'Sol·licitud #' . $supportRequest->id)

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="bi bi-ticket-perforated text-2xl mr-3"></i>
                        <div>
                            <h1 class="text-xl font-semibold">Sol·licitud #{{ $supportRequest->id }}</h1>
                            <p class="text-blue-100 text-sm">
                                Creada el {{ $supportRequest->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $supportRequest->type == 'service' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $supportRequest->type == 'incident' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $supportRequest->type == 'improvement' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $supportRequest->type == 'consultation' ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ $supportRequest->type_label }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $supportRequest->urgency == 'low' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $supportRequest->urgency == 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $supportRequest->urgency == 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $supportRequest->urgency == 'critical' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $supportRequest->urgency_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Detalles de la solicitud -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="bi bi-info-circle mr-2 text-blue-600"></i>
                        Detalls de la sol·licitud
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripció</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $supportRequest->description }}</p>
                            </div>
                        </div>

                        @if($supportRequest->module)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mòdul afectat</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-gray-800">
                                        <i class="bi bi-window mr-2 text-gray-600"></i>
                                        {{ $supportRequest->module }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($supportRequest->url)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">URL d'origen</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <a href="{{ $supportRequest->url }}" 
                                       target="_blank" 
                                       class="text-blue-600 hover:text-blue-800 break-all">
                                        <i class="bi bi-link-45deg mr-2"></i>
                                        {{ $supportRequest->url }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($supportRequest->department)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Departament</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-gray-800">
                                        <i class="bi bi-building mr-2 text-gray-600"></i>
                                        {{ $supportRequest->department }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Estado y resolución -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="bi bi-gear mr-2 text-blue-600"></i>
                        Gestió de l'estat
                    </h2>

                    <form method="POST" action="{{ route('admin.support-requests.update-status', $supportRequest) }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estat actual</label>
                                <div class="flex items-center space-x-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $supportRequest->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $supportRequest->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $supportRequest->status == 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $supportRequest->status == 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $supportRequest->status_label }}
                                    </span>
                                    @if($supportRequest->resolved_at)
                                        <span class="text-sm text-gray-500">
                                            Resolt el {{ $supportRequest->resolved_at->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Canviar estat</label>
                                <select name="status" 
                                        class="border p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="pending" {{ $supportRequest->status == 'pending' ? 'selected' : '' }}>Pendent</option>
                                    <option value="in_progress" {{ $supportRequest->status == 'in_progress' ? 'selected' : '' }}>En procés</option>
                                    <option value="resolved" {{ $supportRequest->status == 'resolved' ? 'selected' : '' }}>Resolt</option>
                                    <option value="closed" {{ $supportRequest->status == 'closed' ? 'selected' : '' }}>Tancat</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notes de resolució</label>
                                <textarea name="resolution_notes" 
                                          rows="4" 
                                          class="border p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Descriu com s'ha resolt la incidència...">{{ $supportRequest->resolution_notes }}</textarea>
                                <p class="text-sm text-gray-500 mt-1">
                                    Obligatori quan l'estat és "Resolt" o "Tancat"
                                </p>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('admin.support-requests.index') }}" 
                                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="bi bi-arrow-left mr-2"></i>Tornar
                                </a>
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="bi bi-check-circle mr-2"></i>Actualitzar estat
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información lateral -->
            <div class="space-y-6">
                <!-- Información del solicitante -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="bi bi-person mr-2 text-blue-600"></i>
                        Informació del sol·licitant
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            @if($supportRequest->user)
                                <img src="{{ $supportRequest->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($supportRequest->name) . '&background=3B82F6&color=fff' }}" 
                                     alt="{{ $supportRequest->name }}" 
                                     class="h-12 w-12 rounded-full mr-3">
                            @else
                                <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                    <i class="bi bi-person text-gray-600 text-xl"></i>
                                </div>
                            @endif
                            
                            <div>
                                <p class="font-medium text-gray-900">{{ $supportRequest->name }}</p>
                                <p class="text-sm text-gray-500">{{ $supportRequest->email }}</p>
                                @if($supportRequest->user)
                                    <p class="text-xs text-blue-600">
                                        <i class="bi bi-shield-check mr-1"></i>Usuari registrat
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400">
                                        <i class="bi bi-person-x mr-1"></i>Usuari anònim
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if($supportRequest->ip_address)
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-600">
                                    <i class="bi bi-geo-alt mr-1"></i>
                                    IP: {{ $supportRequest->ip_address }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Información técnica -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="bi bi-info-square mr-2 text-blue-600"></i>
                        Informació tècnica
                    </h3>
                    
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">ID de sol·licitud</p>
                            <p class="font-mono text-gray-900">#{{ $supportRequest->id }}</p>
                        </div>
                        
                        <div>
                            <p class="text-gray-600">Data de creació</p>
                            <p class="text-gray-900">{{ $supportRequest->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-gray-600">Última actualització</p>
                            <p class="text-gray-900">{{ $supportRequest->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>

                        @if($supportRequest->user_agent)
                            <div>
                                <p class="text-gray-600">Navegador</p>
                                <p class="text-gray-900 text-xs break-all">{{ $supportRequest->user_agent }}</p>
                            </div>
                        @endif

                        @if($supportRequest->resolved_by)
                            <div>
                                <p class="text-gray-600">Resolt per</p>
                                <p class="text-gray-900">
                                    {{ $supportRequest->resolvedBy->name ?? 'Sistema' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="bi bi-lightning mr-2 text-blue-600"></i>
                        Accions ràpides
                    </h3>
                    
                    <div class="space-y-2">
                        <a href="mailto:{{ $supportRequest->email }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                            <i class="bi bi-envelope mr-2"></i>Enviar email
                        </a>
                        
                        @if($supportRequest->url)
                            <a href="{{ $supportRequest->url }}" 
                               target="_blank"
                               class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                                <i class="bi bi-box-arrow-up-right mr-2"></i>Obrir URL
                            </a>
                        @endif
                        
                        <form method="POST" 
                              action="{{ route('admin.support-requests.destroy', $supportRequest) }}" 
                              onsubmit="return confirm('Estàs segur que vols eliminar aquesta sol·licitud?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                                <i class="bi bi-trash mr-2"></i>Eliminar sol·licitud
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
