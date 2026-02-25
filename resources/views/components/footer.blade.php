{{-- Botón flotante de soporte --}}
<div class="fixed bottom-6 right-6 z-50">
    <a href="{{ route('support.form') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-all duration-300 hover:scale-110 flex items-center justify-center group"
       title="Obre el formulari de suport">
        <i class="bi bi-headset text-xl"></i>
        <span class="absolute right-full mr-3 bg-gray-800 text-white px-3 py-1 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
            Suport
        </span>
    </a>
</div>

{{-- Footer principal --}}
<footer class="bg-gray-800 text-white mt-auto border-t border-gray-700">
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Información de la empresa -->
            <div class="col-span-1 md:col-span-2">
                <h4 class="text-lg font-semibold mb-4 text-white">UPG</h4>
                <p class="text-gray-200 mb-4">
                    Plataforma educativa integral per a la gestió de cursos, formació i desenvolupament professional.
                </p>
                <div class="flex items-center space-x-4 text-sm text-gray-300">
                    <span>UPG © 2016-2025</span>
                    <span>•</span>
                    <div class="flex items-center">
                        <i class="bi bi-cc mr-1"></i>
                        Creative Commons
                    </div>
                </div>
                <p class="text-sm text-gray-300 mt-2">
                    El contingut està disponible sota la llicència Atribució - CompartirIgual 4.0 si no s'indica el contrari.
                </p>
            </div>

            <!-- Enlaces rápidos -->
            <div>
                <h4 class="text-lg font-semibold mb-4 text-white">Enllaços</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="text-gray-200 hover:text-white transition-colors">
                            Contacte
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-200 hover:text-white transition-colors">
                            Avís legal
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-200 hover:text-white transition-colors">
                            Codi de conducta
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-200 hover:text-white transition-colors">
                            Publicitat
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Redes sociales -->
            <div>
                <h4 class="text-lg font-semibold mb-4 text-white">Seguiu-nos</h4>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-200 hover:text-white transition-colors" title="Facebook">
                        <i class="bi bi-facebook text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-200 hover:text-white transition-colors" title="Twitter">
                        <i class="bi bi-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-200 hover:text-white transition-colors" title="LinkedIn">
                        <i class="bi bi-linkedin text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-200 hover:text-white transition-colors" title="Instagram">
                        <i class="bi bi-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-200 hover:text-white transition-colors" title="YouTube">
                        <i class="bi bi-youtube text-xl"></i>
                    </a>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-300 mb-2">Butlletí informatiu</p>
                    <div class="flex">
                        <input type="email" 
                               placeholder="El teu email" 
                               class="bg-gray-700 text-white px-3 py-2 rounded-l-lg flex-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-400">
                        <button class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-r-lg text-sm transition-colors">
                            Subscriure
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-6 text-center">
            <p class="text-gray-300 text-sm">
                Desenvolupat amb ❤️ per l'equip d'UPG
            </p>
        </div>
    </div>
</footer>
