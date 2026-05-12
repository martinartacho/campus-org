@extends('catalog.layout')

@section('title', 'Matriculación')

@php
    $cartItemsCount = $validItems->count();
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <i class="bi bi-person-plus-fill text-blue-600 me-3"></i>Matriculación
        </h1>
        <p class="text-xl text-gray-600">
            Completa tus datos para finalizar la matriculación en los cursos seleccionados
        </p>
    </div>

    <form id="registrationForm" method="POST" action="{{ route('registration.store') }}">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Registration Form -->
            <div class="lg:col-span-2">
                <!-- Personal Information -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="bi bi-person me-2"></i>Información Personal
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="first_name" 
                                   value="{{ old('first_name', $user?->first_name) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Apellidos <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="last_name" 
                                   value="{{ old('last_name', $user?->last_name) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email', $user?->email) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono
                            </label>
                            <input type="tel" 
                                   name="phone" 
                                   value="{{ old('phone', $user?->phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                DNI/NIE <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="dni" 
                                   value="{{ old('dni', $student?->dni) }}"
                                   required
                                   placeholder="12345678A"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('dni')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Nacimiento <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="birth_date" 
                                   value="{{ old('birth_date', $student?->birth_date?->format('Y-m-d')) }}"
                                   required
                                   max="{{ now()->subYears(16)->format('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('birth_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="bi bi-geo-alt me-2"></i>Dirección
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Dirección <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="address" 
                                   value="{{ old('address', $student?->address) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ciudad <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="city" 
                                   value="{{ old('city', $student?->city) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('city')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Código Postal <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="postal_code" 
                                   value="{{ old('postal_code', $student?->postal_code) }}"
                                   required
                                   placeholder="08001"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('postal_code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="bi bi-shield-check me-2"></i>Términos y Condiciones
                    </h2>

                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Política de Privacidad</h3>
                            <p class="text-sm text-gray-600 mb-3">
                                Tus datos personales serán tratados conforme a la normativa de protección de datos 
                                y utilizados exclusivamente para la gestión de tu matriculación y comunicación 
                                relacionada con los cursos.
                            </p>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                                Leer política completa <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Condiciones de Matriculación</h3>
                            <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                <li>La matriculación queda confirmada tras el pago completo</li>
                                <li>Las plazas son limitadas y se asignan por orden de pago</li>
                                <li>Se aplicará la política de cancelación vigente</li>
                                <li>El alumno se compromete a cumplir el reglamento del campus</li>
                            </ul>
                        </div>

                        <div class="flex items-start">
                            <input type="checkbox" 
                                   name="accept_terms" 
                                   id="accept_terms"
                                   required
                                   class="mt-1 mr-3">
                            <label for="accept_terms" class="text-sm text-gray-700">
                                Acepto los <a href="#" class="text-blue-600 hover:text-blue-800">términos y condiciones</a> 
                                y la <a href="#" class="text-blue-600 hover:text-blue-800">política de privacidad</a> *
                            </label>
                        </div>
                        @error('accept_terms')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        Resumen de la Matriculación
                    </h2>
                    
                    <!-- Courses List -->
                    <div class="space-y-3 mb-6">
                        @foreach($validItems as $item)
                            <div class="border-b border-gray-200 pb-3 last:border-0">
                                <h4 class="font-semibold text-gray-900 text-sm">{{ $item->course_title }}</h4>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs text-gray-600">{{ $item->course_code }}</span>
                                    @if($item->price_at_time > 0)
                                        <span class="text-sm font-semibold">{{ number_format($item->price_at_time, 2) }} &euro;</span>
                                    @else
                                        <span class="text-sm font-semibold text-green-600">Gratis</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Price Summary -->
                    <div class="space-y-2 mb-6 pt-4 border-t">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span>{{ number_format($validItems->sum('price_at_time'), 2) }} &euro;</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Gastos de gestión:</span>
                            <span>0,00 &euro;</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t">
                            <span>Total:</span>
                            <span>{{ number_format($validItems->sum('price_at_time'), 2) }} &euro;</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex space-x-3">
                        <button type="button" 
                                id="clearBtn"
                                onclick="clearForm()"
                                class="flex-1 bg-gray-500 text-white py-3 rounded-lg hover:bg-gray-600 transition font-semibold">
                            <i class="bi bi-trash me-2"></i>
                            Limpiar Datos
                        </button>
                        <button type="submit" 
                            id="submitBtn"
                            class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <i class="bi bi-credit-card me-2"></i>
                        <span id="submitText">Procedir al Pagament</span>
                        </button>
                    </div>

                    <!-- Loading State -->
                    <div id="loadingState" class="hidden">
                        <div class="text-center py-4">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <p class="text-gray-600 mt-2">Procesando matriculación...</p>
                        </div>
                    </div>

                    <!-- Security Info -->
                    <div class="mt-6 p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-2 text-green-800 text-sm">
                            <i class="bi bi-shield-check"></i>
                            <span>Pagament 100% segur</span>
                        </div>
                        <p class="text-green-700 text-xs mt-2">
                            Processament segur amb Stripe. 
                            Les teves dades estan protegides amb encriptació SSL.
                        </p>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-2">Mètodes de pagament acceptats:</p>
                        <div class="flex space-x-2">
                            <div class="w-12 h-8 bg-gray-100 rounded flex items-center justify-center">
                                <i class="bi bi-credit-card text-gray-600 text-sm"></i>
                            </div>
                            <div class="w-12 h-8 bg-gray-100 rounded flex items-center justify-center">
                                <i class="bi bi-credit-card-2-back text-gray-600 text-sm"></i>
                            </div>
                            <div class="w-12 h-8 bg-gray-100 rounded flex items-center justify-center">
                                <i class="bi bi-paypal text-gray-600 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Help -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600 mb-2">¿Necesitas ayuda?</p>
                        <a href="mailto:campus@upg.cat" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="bi bi-envelope me-1"></i>campus@upg.cat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('registrationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingState = document.getElementById('loadingState');
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.textContent = 'Procesando...';
    loadingState.classList.remove('hidden');
    
    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (data.free) {
                // Free courses - redirect directly
                window.location.href = data.redirect_url;
            } else {
                // Paid courses - redirect to Stripe
                window.location.href = data.redirect_url;
            }
        } else {
            throw new Error(data.message || 'Error en la matriculación');
        }
        
    } catch (error) {
        console.error('Registration error:', error);
        
        // Show error
        submitBtn.disabled = false;
        submitText.textContent = 'Proceder al Pago';
        loadingState.classList.add('hidden');
        
        showToast(error.message || 'Error al procesar la matriculación', 'error');
    }
});

// Clear form function
function clearForm() {
    const form = document.getElementById('registrationForm');
    
    // Clear all input fields
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(field => {
        field.value = '';
        field.classList.remove('border-red-500', 'border-green-500');
    });
    
    // Clear validation messages
    const errorMessages = form.querySelectorAll('.text-red-500');
    errorMessages.forEach(msg => msg.remove());
    
    // Show success message
    showToast('Formulario limpiado correctamente', 'success');
}

// Form validation
function validateForm() {
    const form = document.getElementById('registrationForm');
    const required = form.querySelectorAll('[required]');
    let isValid = true;
    
    required.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}
</script>
@endsection
