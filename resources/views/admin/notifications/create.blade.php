@extends('campus.shared.layout')

@section('title', 'Crear Notificació')
@section('subtitle', 'Enviar notificacions als usuaris')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
@endpush

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
                        <div class="border rounded-lg overflow-hidden">
                            <div id="editor-toolbar" class="bg-gray-50 border-b p-2 flex items-center gap-2">
                                <button type="button" id="template-btn" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                    <i class="bi bi-file-earmark-text mr-1"></i>Plantilla
                                </button>
                                <div class="text-sm text-gray-600">
                                    <span id="char-count">0</span> caràcters
                                </div>
                            </div>
                            <textarea id="content-editor" 
                                      name="content" 
                                      required
                                      rows="12"
                                      class="w-full border-0 focus:ring-0"
                                      placeholder="Introdueix el contingut de la notificació"></textarea>
                        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recipientType = document.getElementById('recipientType');
    const specificRecipients = document.getElementById('specificRecipients');
    const roleRecipient = document.getElementById('roleRecipient');
    const rolesRecipients = document.getElementById('rolesRecipients');
    const filteredRecipients = document.getElementById('filteredRecipients');
    const userSelect = document.getElementById('userSelect');
    const notificationType = document.getElementById('notification-type');
    const templateBtn = document.getElementById('template-btn');
    const contentEditor = document.getElementById('content-editor');
    const charCount = document.getElementById('char-count');

    // Initialize Summernote
    $(contentEditor).summernote({
        lang: 'ca-ES',
        height: 300,
        toolbar: [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['para', ['ul', 'ol', 'paragraph', 'height']],
            ['insert', ['link', 'picture', 'video', 'table', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onInit: function() {
                $('.note-editable').css('min-height', '200px');
                updateCharCount();
            },
            onChange: function() {
                updateCharCount();
            }
        }
    });

    // Character counter
    function updateCharCount() {
        const content = $(contentEditor).summernote('code');
        const cleanContent = content.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ');
        charCount.textContent = cleanContent.length;
    }

    // Template dropdown
    const templates = {
        support: {
            name: '⚠️ Plantilla de Suport',
            content: `⚠️ **NOVA SOL·LICITUD DE SUPORT ASSIGNADA**

📋 **Detalls del Ticket:**
- **Número de Ticket:** [TICKET_ID]
- **Remitent:** [SENDER_NAME] ([SENDER_EMAIL])
- **Departament:** [DEPARTMENT]
- **Tipus:** [TYPE]
- **Urgència:** [URGENCY]
- **Data:** [DATE]

📝 **Descripció:** 
[DESCRIPTION]

🎯 **Acció Requerida:**
[ACTION_REQUIRED]

Podeu gestionar aquesta sol·licitud a través del sistema de notificaciones o contactar directament amb el remitent.
Destinataris: Usuaris amb rol: [TARGET_ROLES]
Estat: Publicat el [PUBLISHED_DATE]`
        },
        academic: {
            name: '📚 Plantilla Acadèmica',
            content: `📚 **COMUNICACIÓ ACADÈMICA**

📅 **Data:** ${new Date().toLocaleDateString('ca-ES')}

📝 **Contingut:**
[AQUÍ EL CONTINGUT]

---
*Departament d'Acadèmia - Campus UPG*`
        },
        administrative: {
            name: '⚙️ Plantilla Administrativa',
            content: `⚙️ **COMUNICACIÓ ADMINISTRATIVA**

📅 **Data:** ${new Date().toLocaleDateString('ca-ES')}

📝 **Contingut:**
[AQUÍ EL CONTINGUT]

---
*Departament d'Administració - Campus UPG*`
        }
    };

    // Template button functionality
    let currentTemplate = null;
    
    templateBtn.addEventListener('click', function() {
        const type = notificationType.value;
        
        if (!type || !templates[type]) {
            alert('Selecciona un tipus de notificació per veure les plantilles disponibles');
            return;
        }

        const template = templates[type];
        
        if (currentTemplate === type) {
            // If same template is selected, clear content
            $(contentEditor).summernote('code', '');
            currentTemplate = null;
            templateBtn.innerHTML = '<i class="bi bi-file-earmark-text mr-1"></i>Plantilla';
            templateBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            templateBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        } else {
            // Apply template
            $(contentEditor).summernote('code', template.content);
            currentTemplate = type;
            templateBtn.innerHTML = '<i class="bi bi-check-circle mr-1"></i>Aplicada';
            templateBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            templateBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        }
        
        updateCharCount();
    });

    // Update template button when type changes
    notificationType.addEventListener('change', function() {
        currentTemplate = null;
        templateBtn.innerHTML = '<i class="bi bi-file-earmark-text mr-1"></i>Plantilla';
        templateBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
        templateBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
    });

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
@endsection
