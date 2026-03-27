@extends('campus.shared.layout')

@section('title', 'Crear Notificació')
@section('subtitle', 'Enviar notificacions als usuaris')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="bi bi-bell-fill text-blue-600 mr-3"></i>
                Crear Notificació
            </h1>
            <p class="text-gray-600">
                Envia notificacions a usuaris específics, per rol o segons filtres
            </p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.notifications.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Títol *</label>
                        <input type="text" 
                               name="title" 
                               required
                               maxlength="255"
                               class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Introdueix el títol de la notificació">
                    </div>

                    <!-- Contenido -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contingut *</label>
                        <textarea name="content" 
                                  id="content-editor" 
                                  class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  rows="10" 
                                  required
                                  placeholder="Introdueix el contingut de la notificació">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipo de destinatario -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipus de destinatari *</label>
                        <select name="recipient_type" 
                                id="recipientType"
                                required
                                class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecciona una opció</option>
                            <option value="specific">Usuaris específics</option>
                            <option value="role">Rol específic</option>
                            <option value="roles">Múltiples rols</option>
                            <option value="filtered">Usuaris filtrats</option>
                        </select>
                    </div>

                    <!-- Tipo de notificación -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipus de notificació</label>
                        <select name="type" 
                                id="notification-type"
                                class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecciona una opció</option>
                            <option value="general">📢 General</option>
                            <option value="support">⚠️ Suport</option>
                            <option value="academic">📚 Acadèmic</option>
                            <option value="administrative">⚙️ Administratiu</option>
                        </select>
                    </div>

                    <!-- Destinatarios específicos -->
                    <div id="specificRecipients" class="md:col-span-2 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usuaris</label>
                        <select name="recipient_ids[]" 
                                id="userSelect"
                                multiple
                                class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <!-- Options will be loaded via AJAX -->
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Pots seleccionar múltiples usuaris mantenent premut Ctrl/Cmd
                        </p>
                    </div>

                    <!-- Rol específico -->
                    <div id="roleRecipient" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                        <select name="recipient_role" 
                                class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecciona un rol</option>
                            <option value="admin">Administració</option>
                            <option value="director">Director</option>
                            <option value="manager">Manager</option>
                            <option value="coordinacio">Coordinació</option>
                            <option value="gestio">Gestió</option>
                            <option value="comunicacio">Comunicació</option>
                            <option value="secretaria">Secretaria</option>
                            <option value="editor">Editor</option>
                            <option value="treasury">Tresoreria</option>
                        </select>
                    </div>

                    <!-- Múltiples roles -->
                    <div id="rolesRecipients" class="md:col-span-2 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rols</label>
                        <select name="recipient_roles[]" 
                                multiple
                                class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="admin">Administració</option>
                            <option value="director">Director</option>
                            <option value="manager">Manager</option>
                            <option value="coordinacio">Coordinació</option>
                            <option value="gestio">Gestió</option>
                            <option value="comunicacio">Comunicació</option>
                            <option value="secretaria">Secretaria</option>
                            <option value="editor">Editor</option>
                            <option value="treasury">Tresoreria</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Pots seleccionar múltiples rols mantenent premut Ctrl/Cmd
                        </p>
                    </div>

                    <!-- Filtros -->
                    <div id="filteredRecipients" class="md:col-span-2 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filtres d'usuaris</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rols</label>
                                <select name="filters[roles][]" 
                                        multiple
                                        class="w-full border p-2 rounded-lg text-sm">
                                    <option value="admin">Administració</option>
                                    <option value="director">Director</option>
                                    <option value="manager">Manager</option>
                                    <option value="coordinacio">Coordinació</option>
                                    <option value="gestio">Gestió</option>
                                    <option value="comunicacio">Comunicació</option>
                                    <option value="secretaria">Secretaria</option>
                                    <option value="editor">Editor</option>
                                    <option value="treasury">Tresoreria</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cerca</label>
                                <input type="text" 
                                       name="filters[search]" 
                                       class="w-full border p-2 rounded-lg text-sm"
                                       placeholder="Nom o email de l'usuari...">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            La notificació s'enviarà a tots els usuaris que coincideixin amb els filtres seleccionats.
                        </p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-6 flex justify-end space-x-4">
                    <a href="{{ route('admin.notifications.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel·lar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="bi bi-send mr-2"></i>
                        Enviar Notificació
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@stack('scripts')
<script>
$(document).ready(function() {
    // Initialize Summernote
    $('#content-editor').summernote({
        lang: 'ca-ES',
        height: 300,
        toolbar: [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video', 'table', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onInit: function() {
                $('.note-editable').css('min-height', '200px');
            }
        }
    });

    // Recipient type functionality
    const recipientType = document.getElementById('recipientType');
    const specificRecipients = document.getElementById('specificRecipients');
    const roleRecipient = document.getElementById('roleRecipient');
    const rolesRecipients = document.getElementById('rolesRecipients');
    const filteredRecipients = document.getElementById('filteredRecipients');
    const userSelect = document.getElementById('userSelect');

    // Load users for specific recipients
    fetch('/admin/notifications/users')
        .then(response => response.json())
        .then(users => {
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.name} (${user.email})`;
                userSelect.appendChild(option);
            });
        });

    recipientType.addEventListener('change', function() {
        // Hide all sections
        specificRecipients.classList.add('hidden');
        roleRecipient.classList.add('hidden');
        rolesRecipients.classList.add('hidden');
        filteredRecipients.classList.add('hidden');

        // Show relevant section
        switch(this.value) {
            case 'specific':
                specificRecipients.classList.remove('hidden');
                break;
            case 'role':
                roleRecipient.classList.remove('hidden');
                break;
            case 'roles':
                rolesRecipients.classList.remove('hidden');
                break;
            case 'filtered':
                filteredRecipients.classList.remove('hidden');
                break;
        }
    });
});
</script>
@endstack
@endsection
