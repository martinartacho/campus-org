@extends('campus.shared.layout')

@section('title', 'Espais - Gestió de Recursos')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Espais</h1>
        <div class="flex gap-4">
            <button onclick="openCreateModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Nou Espai
            </button>
            <a href="{{ route('campus.resources.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Tornar
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipus d'espai</label>
                <select id="filterType" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tots</option>
                    @foreach(App\Models\CampusSpace::TYPES as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estat</label>
                <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tots</option>
                    <option value="1">Actius</option>
                    <option value="0">Inactius</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="filterSpaces()" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
            </div>
        </div>
    </div>

    {{-- Llista d'espais --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacitat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipament</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="spacesTableBody">
                    @foreach($spaces as $space)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $space->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $space->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ App\Models\CampusSpace::TYPES[$space->type] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $space->formatted_capacity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $space->formatted_equipment }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($space->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Actiu</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactiu</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="editSpace({{ $space->id }})" class="text-info-600 hover:text-info-800 mr-3 px-2 py-1 rounded border border-info-300 hover:bg-info-50">
                                <i class="bi bi-pencil-fill mr-1"></i>Editar
                            </button>
                            <button onclick="deleteSpace({{ $space->id }})" class="text-danger-600 hover:text-danger-800 px-2 py-1 rounded border border-danger-300 hover:bg-danger-50">
                                <i class="bi bi-trash-fill mr-1"></i>Eliminar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Crear/Editar --}}
    <div id="spaceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Nou Espai</h3>
                <form id="spaceForm">
                    <input type="hidden" id="spaceId" name="id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Codi *</label>
                        <div class="flex space-x-2">
                            <input type="text" id="code" name="code" required maxlength="10"
                                   placeholder="Preferiblemente Auto, o escribe manualmente"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="generateSpaceCode()" 
                                    class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 text-sm">
                                <i class="bi bi-magic mr-1"></i>Auto
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Preferiblemente usa "Auto" para generar desde el nombre y tipo, o escribe manualmente si necesitas un código especial</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                        <input type="text" id="name" name="name" required maxlength="255"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Capacitat *</label>
                        <input type="number" id="capacity" name="capacity" required min="1" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipus *</label>
                        <select id="type" name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(App\Models\CampusSpace::TYPES as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripció</label>
                        <textarea id="description" name="description" rows="3" maxlength="500"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Equipament</label>
                        <textarea id="equipment" name="equipment" rows="3" maxlength="255"
                                  placeholder="Indica si precisa: Projector, TV, Àudio, Ordinadors, ..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Separa los elementos con comas. Ej: Projector, TV, Àudio</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" checked class="mr-2">
                            <span>Espai actiu</span>
                        </label>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                            Cancel·lar
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function generateSpaceCode() {
    const name = document.getElementById('name').value;
    const type = document.getElementById('type').value;
    
    if (name && type) {
        // Generar código basado en el tipo y nombre
        let baseCode = '';
        
        switch(type) {
            case 'sala_actes':
                baseCode = 'SA';
                break;
            case 'mitjana':
                baseCode = 'AM';
                break;
            case 'petita':
                baseCode = 'AP';
                break;
            case 'polivalent':
                baseCode = 'SP';
                break;
            case 'extern':
                // Para tipo extern, usar las primeras 3-4 letras del nombre en mayúsculas
                baseCode = name.substring(0, 4).toUpperCase().replace(/[^A-Z]/g, '');
                if (baseCode.length < 2) baseCode = 'EXT';
                break;
            default:
                baseCode = 'XX';
        }
        
        // Para tipos que pueden tener múltiples (mitjana, petita), añadir número si no existe
        if (type === 'mitjana' || type === 'petita') {
            // Aquí podríamos verificar si ya existen códigos similares y añadir número
            // Por ahora, usaremos el primer código disponible
            const existingCodes = ['1', '2', '3', '4', '5'];
            baseCode += existingCodes[0]; // Por defecto AM1, AP1
        }
        
        document.getElementById('code').value = baseCode;
        
        // Poner foco en el código para que el usuario pueda modificarlo fácilmente
        document.getElementById('code').focus();
        document.getElementById('code').select();
    } else {
        alert('Por favor, introduce primero el nombre y selecciona el tipo');
    }
}

// Auto-generar código cuando cambian nombre o tipo (solo si el campo está vacío)
document.getElementById('name').addEventListener('change', function() {
    const codeField = document.getElementById('code');
    const typeField = document.getElementById('type');
    if (!codeField.value && typeField.value) {
        generateSpaceCode();
    }
});

document.getElementById('type').addEventListener('change', function() {
    const codeField = document.getElementById('code');
    const nameField = document.getElementById('name');
    if (!codeField.value && nameField.value) {
        generateSpaceCode();
    }
});

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Nou Espai';
    document.getElementById('spaceForm').reset();
    document.getElementById('spaceId').value = '';
    document.getElementById('spaceModal').classList.remove('hidden');
}

function editSpace(id) {
    fetch(`/campus/resources/spaces/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Editar Espai';
            document.getElementById('spaceId').value = data.id;
            document.getElementById('code').value = data.code;
            document.getElementById('name').value = data.name;
            document.getElementById('capacity').value = data.capacity;
            document.getElementById('type').value = data.type;
            document.getElementById('description').value = data.description || '';
            document.getElementById('equipment').value = data.equipment || '';
            document.getElementById('is_active').checked = data.is_active;
            
            document.getElementById('spaceModal').classList.remove('hidden');
        });
}

function closeModal() {
    document.getElementById('spaceModal').classList.add('hidden');
}

function deleteSpace(id) {
    if (confirm('Estàs segur que vols eliminar aquest espai?')) {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        
        fetch(`/campus/resources/spaces/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Ha ocurrido un error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    }
}

function filterSpaces() {
    const type = document.getElementById('filterType').value;
    const status = document.getElementById('filterStatus').value;
    
    const params = new URLSearchParams();
    if (type) params.append('type', type);
    if (status) params.append('is_active', status);
    
    window.location.href = `/campus/resources/spaces?${params.toString()}`;
}

document.getElementById('spaceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const spaceId = document.getElementById('spaceId').value;
    const url = spaceId ? `/campus/resources/spaces/${spaceId}` : '/campus/resources/spaces';
    
    // Añadir _method para Laravel
    if (spaceId) {
        formData.append('_method', 'PUT');
    }
    
    // Manejar checkbox is_active - enviar siempre como 0 o 1
    const isActiveCheckbox = document.getElementById('is_active');
    formData.set('is_active', isActiveCheckbox.checked ? '1' : '0');
    
    fetch(url, {
        method: 'POST', // Siempre usar POST con _method
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Ha ocurrido un error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
});
</script>
@endsection
