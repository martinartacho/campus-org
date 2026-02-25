@extends('campus.shared.layout')

@section('title', 'Franjes Horàries - Gestió de Recursos')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Franjes Horàries</h1>
        <div class="flex gap-4">
            <button onclick="openCreateModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Nova Franja
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Dia de la setmana</label>
                <select id="filterDay" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tots</option>
                    @foreach(App\Models\CampusTimeSlot::DAYS as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Codi de franja</label>
                <select id="filterCode" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tots</option>
                    @foreach(App\Models\CampusTimeSlot::TIME_CODES as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="filterTimeSlots()" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
            </div>
        </div>
    </div>

    {{-- Llista de franjes --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora inici</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora fi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripció</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignacions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="timeSlotsTableBody">
                    @foreach($timeSlots as $timeSlot)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $timeSlot->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $timeSlot->day_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $timeSlot->start_time->format('H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $timeSlot->end_time->format('H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $timeSlot->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($timeSlot->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactiva</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                {{ $timeSlot->courseSchedules->count() }} assignacions
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="editTimeSlot({{ $timeSlot->id }})" class="text-info-600 hover:text-info-800 mr-3 px-2 py-1 rounded border border-info-300 hover:bg-info-50">
                                <i class="bi bi-pencil-fill mr-1"></i>Editar
                            </button>
                            <button onclick="deleteTimeSlot({{ $timeSlot->id }})" class="text-danger-600 hover:text-danger-800 px-2 py-1 rounded border border-danger-300 hover:bg-danger-50">
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
    <div id="timeSlotModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Nova Franja Horària</h3>
                <form id="timeSlotForm">
                    <input type="hidden" id="timeSlotId" name="id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dia de la setmana *</label>
                        <select id="day_of_week" name="day_of_week" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(App\Models\CampusTimeSlot::DAYS as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Codi *</label>
                        <div class="flex space-x-2">
                            <input type="text" id="code" name="code" required maxlength="20"
                                   placeholder="Preferiblemente Auto, o escribe manualmente"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="generateCode()" 
                                    class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 text-sm">
                                <i class="bi bi-magic mr-1"></i>Auto
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Preferiblemente usa "Auto" para generar desde las horas, o escribe manualmente si necesitas un código especial</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hora d'inici *</label>
                        <input type="time" id="start_time" name="start_time" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hora de fi *</label>
                        <input type="time" id="end_time" name="end_time" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripció *</label>
                        <input type="text" id="description" name="description" required maxlength="255"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" checked class="mr-2">
                            <span>Franja activa</span>
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
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Nova Franja Horària';
    document.getElementById('timeSlotForm').reset();
    document.getElementById('timeSlotId').value = '';
    document.getElementById('timeSlotModal').classList.remove('hidden');
}

function generateCode() {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    
    if (startTime && endTime) {
        // Extraer hora y minutos de inicio
        const [startHour, startMinute] = startTime.split(':').map(Number);
        const [endHour, endMinute] = endTime.split(':').map(Number);
        
        // Generar código base
        let periodCode;
        if (startHour < 14) {
            periodCode = 'M' + startHour; // M11, M10, etc.
        } else {
            periodCode = 'T' + startHour; // T16, T18, etc.
        }
        
        // Si los minutos de inicio no son 00, añadirlos
        if (startMinute !== 0) {
            periodCode += startMinute.toString().padStart(2, '0'); // M1130 para 11:30
        }
        
        // Si la hora de fin es diferente del estándar (30 min después), añadir sufijo
        const standardEndMinute = (startMinute + 30) % 60;
        const standardEndHour = startHour + Math.floor((startMinute + 30) / 60);
        
        if (endHour !== standardEndHour || endMinute !== standardEndMinute) {
            // Añadir sufijo de fin si no es el estándar
            const endSuffix = endHour.toString().padStart(2, '0') + endMinute.toString().padStart(2, '0');
            periodCode += '_' + endSuffix; // M11_1200 para 11:00-12:00
        }
        
        // Generar descripción completa
        const period = startHour < 14 ? 'Matí' : 'Tarda';
        const description = `${period} ${startTime}-${endTime}`;
        
        // Actualizar campos
        document.getElementById('code').value = periodCode;
        document.getElementById('description').value = description;
        
        // Poner foco en la descripción para que el usuario pueda modificarla fácilmente
        document.getElementById('description').focus();
        document.getElementById('description').select();
    } else {
        alert('Por favor, selecciona primero la hora de inicio y fin');
    }
}

// Auto-generar código cuando cambian las horas (solo si el campo está vacío)
document.getElementById('start_time').addEventListener('change', function() {
    const codeField = document.getElementById('code');
    if (!codeField.value && document.getElementById('end_time').value) {
        generateCode();
    }
});

document.getElementById('end_time').addEventListener('change', function() {
    const codeField = document.getElementById('code');
    if (!codeField.value && document.getElementById('start_time').value) {
        generateCode();
    }
});

function editTimeSlot(id) {
    fetch(`/campus/resources/timeslots/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Editar Franja Horària';
            document.getElementById('timeSlotId').value = data.id;
            document.getElementById('day_of_week').value = data.day_of_week;
            document.getElementById('code').value = data.code;
            document.getElementById('start_time').value = data.start_time;
            document.getElementById('end_time').value = data.end_time;
            document.getElementById('description').value = data.description;
            document.getElementById('is_active').checked = data.is_active;
            
            document.getElementById('timeSlotModal').classList.remove('hidden');
        });
}

function closeModal() {
    document.getElementById('timeSlotModal').classList.add('hidden');
}

function deleteTimeSlot(id) {
    if (confirm('Estàs segur que vols eliminar aquesta franja horària?')) {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        
        fetch(`/campus/resources/timeslots/${id}`, {
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

function filterTimeSlots() {
    const day = document.getElementById('filterDay').value;
    const code = document.getElementById('filterCode').value;
    
    const params = new URLSearchParams();
    if (day) params.append('day_of_week', day);
    if (code) params.append('code', code);
    
    window.location.href = `/campus/resources/timeslots?${params.toString()}`;
}

document.getElementById('timeSlotForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const timeSlotId = document.getElementById('timeSlotId').value;
    const url = timeSlotId ? `/campus/resources/timeslots/${timeSlotId}` : '/campus/resources/timeslots';
    
    // Añadir _method para Laravel
    if (timeSlotId) {
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
