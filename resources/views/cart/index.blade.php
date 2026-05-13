@extends('catalog.layout')

@section('title', 'Mi Carrito')

@php
    $cartItemsCount = $cart->items_count ?? 0;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <i class="bi bi-cart-fill text-blue-600 me-3"></i>Mi Carrito
        </h1>
        <p class="text-xl text-gray-600">
            Revisa tus cursos seleccionados antes de proceder con la matriculación
        </p>
    </div>

    @if($cart && !$cart->isEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            Cursos Seleccionados ({{ $cart->items_count }})
                        </h2>
                        <button onclick="clearCart()" 
                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                            <i class="bi bi-trash me-1"></i>Vaciar Carrito
                        </button>
                    </div>

                    <!-- Valid Items -->
                    @if($validItems->count() > 0)
                        <div class="space-y-4 mb-8">
                            @foreach($validItems as $item)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                        <!-- Course Info -->
                                        <div class="flex-1 mb-4 md:mb-0">
                                            <div class="flex items-start space-x-4">
                                                <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="bi bi-book text-blue-600 text-xl"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                                        {{ $item->course_title }}
                                                    </h3>
                                                    <p class="text-gray-600 text-sm mb-2">
                                                        {{ $item->course_code }}
                                                    </p>
                                                    
                                                    <!-- Course Meta -->
                                                    <div class="flex flex-wrap gap-2 mb-3">
                                                        @if($item->course->level)
                                                            <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                                                {{ \App\Models\CampusCourse::LEVELS[$item->course->level] ?? $item->course->level }}
                                                            </span>
                                                        @endif
                                                        @if($item->course->format)
                                                            <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                                                                <i class="bi bi-{{ $item->course->format === 'online' ? 'camera-video' : ($item->course->format === 'hybrid' ? 'display' : 'geo-alt') }} me-1"></i>
                                                                {{ ucfirst($item->course->format) }}
                                                            </span>
                                                        @endif
                                                        @if($item->course->hours)
                                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                                                <i class="bi bi-clock me-1"></i>{{ $item->course->hours }}h
                                                            </span>
                                                        @endif
                                                        @if($item->course->start_date)
                                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                                <i class="bi bi-calendar3 me-1"></i>{{ $item->course->start_date->format('d/m/Y') }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <!-- Availability Info -->
                                                    @if($item->course->max_students)
                                                        <div class="text-sm text-gray-600">
                                                            <span>Plazas: </span>
                                                            <span class="font-semibold {{ $item->course->available_spots > 5 ? 'text-green-600' : ($item->course->available_spots > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                                                {{ $item->course->available_spots }} / {{ $item->course->max_students }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Price and Actions -->
                                        <div class="flex flex-col items-end space-y-3">
                                            <div class="text-right">
                                                @if($item->price_at_time > 0)
                                                    <div class="text-2xl font-bold text-gray-900">
                                                        {{ number_format($item->price_at_time, 2) }} &euro;
                                                    </div>
                                                @else
                                                    <div class="text-2xl font-bold text-green-600">GRATIS</div>
                                                @endif
                                                <div class="text-sm text-gray-500">Precio por curso</div>
                                            </div>
                                            
                                            <div class="flex space-x-2">
                                                <a href="{{ route('catalog.show', $item->course_id) }}" 
                                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    <i class="bi bi-eye me-1"></i>Ver
                                                </a>
                                                <button onclick="removeFromCart({{ $item->course_id }})" 
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    <i class="bi bi-trash me-1"></i>Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Invalid Items -->
                    @if(count($invalidItems) > 0)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold text-red-800 mb-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Problemas con algunos cursos
                            </h3>
                            <p class="text-red-700 text-sm mb-4">
                                Los siguientes cursos tienen problemas y deben ser eliminados del carrito:
                            </p>
                            
                            <div class="space-y-3">
                                @foreach($invalidItems as $invalidItem)
                                    <div class="bg-white rounded-lg p-3 border border-red-200">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 mb-1">
                                                    {{ $invalidItem['item']->course_title }}
                                                </h4>
                                                <ul class="text-sm text-red-600 space-y-1">
                                                    @foreach($invalidItem['issues'] as $issue)
                                                        <li>· {{ $issue }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <button onclick="removeInvalidItem({{ $invalidItem['item']->course_id }})" 
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium ml-4">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                <button onclick="removeInvalidItems()" 
                                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition text-sm">
                                    <i class="bi bi-trash me-2"></i>Eliminar todos los inválidos
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        Resumen del Pedido
                    </h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal ({{ $validItems->count() }} cursos):</span>
                            <span>{{ number_format($validItems->sum('subtotal'), 2) }} &euro;</span>
                        </div>
                        
                        <!-- No additional fees for now -->
                        <div class="flex justify-between text-gray-600">
                            <span>Gastos de gestión:</span>
                            <span>0,00 &euro;</span>
                        </div>
                        
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold text-gray-900">
                                <span>Total:</span>
                                <span>{{ number_format($validItems->sum('subtotal'), 2) }} &euro;</span>
                            </div>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    @if(count($invalidItems) > 0)
                        <button disabled 
                                class="w-full bg-gray-400 text-white py-3 rounded-lg cursor-not-allowed font-semibold mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Corrige los problemas antes de continuar
                        </button>
                    @else
                        <a href="{{ route('registration.create') }}" 
                           class="block w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold text-center mb-4">
                            <i class="bi bi-credit-card me-2"></i>
                            Procedir al Pagament
                        </a>
                    @endif

                    <!-- Continue Shopping -->
                    <a href="{{ route('catalog.index') }}" 
                       class="block w-full text-center text-blue-600 hover:text-blue-800 font-medium">
                        <i class="bi bi-arrow-left me-1"></i>Seguir comprando
                    </a>

                    <!-- Security Info -->
                    <div class="mt-6 p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-2 text-green-800 text-sm">
                            <i class="bi bi-shield-check"></i>
                            <span>Pagament segur amb Stripe</span>
                        </div>
                        <p class="text-green-700 text-xs mt-2">
                            Les teves dades de pagament estan encriptades i segures. 
                            No almacenamos información de tarjetas de crédito.
                        </p>
                    </div>

                    <!-- Help -->
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-semibold text-blue-900 mb-2">¿Necesitas ayuda?</h4>
                        <p class="text-blue-800 text-sm mb-2">
                            Si tienes dudas sobre el proceso de matriculación:
                        </p>
                        <div class="space-y-1 text-sm">
                            <a href="mailto:{{ env('MAIL_ADDRESS_CONTACTE', 'info@campus.org') }}" class="text-blue-600 hover:text-blue-800 block">
                                <i class="bi bi-envelope me-1"></i>{{ env('MAIL_ADDRESS_CONTACTE', 'info@campus.org') }}
                            </a>
                            <a href="tel:{{ env('PHONE_CONTACTE', '+34900123456') }}" class="text-blue-600 hover:text-blue-800 block">
                                <i class="bi bi-telephone me-1"></i>{{ env('PHONE_CONTACTE', '+34 900 123 456') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="bi bi-cart-x text-gray-400 text-4xl"></i>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                Tu carrito está vacío
            </h2>
            
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                Parece que aún no has añadido ningún curso a tu carrito. 
                Explora nuestro catálogo y encuentra el curso perfecto para ti.
            </p>
            
            <div class="space-x-4">
                <a href="{{ route('catalog.index') }}" 
                   class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="bi bi-search me-2"></i>Explorar Cursos
                </a>
                
                @if(auth()->check())
                    <a href="{{ route('dashboard') }}" 
                       class="inline-block px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold">
                        <i class="bi bi-house me-2"></i>Mi Campus
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-block px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
function clearCart() {
    if (confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
        fetch('/carrito/clear', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Error al vaciar el carrito', 'error');
        });
    }
}

function removeInvalidItem(courseId) {
    removeFromCart(courseId);
}

function removeInvalidItems() {
    fetch('/carrito/invalid', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Error al eliminar elementos inválidos', 'error');
    });
}
</script>
@endsection
