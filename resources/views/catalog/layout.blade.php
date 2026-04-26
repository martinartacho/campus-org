<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Campus Virtual') - Catàleg de Cursos</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom Styles -->
    <style>
        .course-card {
            transition: all 0.3s ease;
        }
        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .badge-level {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .cart-badge {
            animation: bounce 0.5s ease-in-out;
        }
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('catalog.index') }}" class="flex items-center space-x-2">
                        <i class="bi bi-mortarboard-fill text-blue-600 text-2xl"></i>
                        <span class="font-bold text-xl text-gray-900">Campus Virtual</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('catalog.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i>
                        Catàleg
                    </a>
                    <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition relative">
                        <i class="bi bi-cart-fill me-1"></i>
                        Carrito
                        @if($cartItemsCount > 0)
                            <span class="cart-badge absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $cartItemsCount }}
                            </span>
                        @endif
                    </a>
                    @if(auth()->check())
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium transition">
                            <i class="bi bi-person-circle me-1"></i>
                            Mi Campus
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Iniciar Sesión
                        </a>
                    @endif
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-700 hover:text-blue-600" onclick="toggleMobileMenu()">
                        <i class="bi bi-list text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('catalog.index') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i>Catàleg
                </a>
                <a href="{{ route('cart.index') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md relative">
                    <i class="bi bi-cart-fill me-2"></i>Carrito
                    @if($cartItemsCount > 0)
                        <span class="absolute top-2 right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $cartItemsCount }}
                        </span>
                    @endif
                </a>
                @if(auth()->check())
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">
                        <i class="bi bi-person-circle me-2"></i>Mi Campus
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mx-4 mt-4">
            <div class="flex items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mx-4 mt-4">
            <div class="flex items-center">
                <i class="bi bi-x-circle-fill me-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Campus Virtual</h3>
                    <p class="text-gray-300 text-sm">
                        Plataforma de formación online con cursos de alta calidad para tu desarrollo profesional y personal.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('catalog.index') }}" class="text-gray-300 hover:text-white">Catàleg de Cursos</a></li>
                        <li><a href="{{ route('cart.index') }}" class="text-gray-300 hover:text-white">Mi Carrito</a></li>
                        @if(auth()->check())
                            <li><a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white">Mi Campus</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-gray-300 hover:text-white">Iniciar Sesión</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                    <div class="space-y-2 text-sm text-gray-300">
                        <p><i class="bi bi-envelope me-2"></i>info@campus.org</p>
                        <p><i class="bi bi-telephone me-2"></i>+34 900 123 456</p>
                        <p><i class="bi bi-geo-alt me-2"></i>Barcelona, España</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} Campus Virtual. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Cart functionality
        let cartCount = {{ $cartItemsCount ?? 0 }};

        function updateCartBadge(count) {
            const badge = document.querySelector('.cart-badge');
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.add('cart-badge');
                    setTimeout(() => badge.classList.remove('cart-badge'), 500);
                } else {
                    badge.style.display = 'none';
                }
            }
            cartCount = count;
        }

        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // AJAX helper for cart operations
        async function addToCart(courseId) {
            try {
                const response = await fetch(`/carrito/add/${courseId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    updateCartBadge(data.cart_count);
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'error');
                }
            } catch (error) {
                showToast('Error al añadir al carrito', 'error');
            }
        }

        async function removeFromCart(courseId) {
            try {
                const response = await fetch(`/carrito/remove/${courseId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    updateCartBadge(data.cart_count);
                    showToast(data.message, 'success');
                    if (window.location.pathname === '/carrito') {
                        location.reload();
                    }
                } else {
                    showToast(data.message, 'error');
                }
            } catch (error) {
                showToast('Error al eliminar del carrito', 'error');
            }
        }

        // Toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-20 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
            
            const colors = {
                success: 'bg-green-500 text-white',
                error: 'bg-red-500 text-white',
                info: 'bg-blue-500 text-white'
            };
            
            toast.classList.add(...colors[type].split(' '));
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }
    </script>

    @yield('scripts')
</body>
</html>
