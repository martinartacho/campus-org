 @extends('campus.shared.layout')

@section('title', 'Re-Cursos - Calendari' . ($coursesCount > 0 ? ' (' . $coursesCount . ' cursos)' : ' sense cursos'))

@section('content')
<script>
let selectedDay = null;
let selectedTimeSlot = null;

function openAssignModal(dayOfWeek = null, timeSlotId = null) {
    selectedDay = dayOfWeek;
    selectedTimeSlot = timeSlotId;
    
    if (timeSlotId) {
        document.querySelector('select[name="time_slot_id"]').value = timeSlotId;
    }
    
    document.getElementById('assignModal').classList.remove('hidden');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    document.getElementById('assignForm').reset();
}

function openCreateCourseModal() {
    document.getElementById('createCourseModal').classList.remove('hidden');
}

function closeCreateCourseModal() {
    document.getElementById('createCourseModal').classList.add('hidden');
    document.getElementById('createCourseForm').reset();
}

function openCreateSpaceModal() {
    document.getElementById('createSpaceModal').classList.remove('hidden');
}

function closeCreateSpaceModal() {
    document.getElementById('createSpaceModal').classList.add('hidden');
    document.getElementById('createSpaceForm').reset();
}

function openCreateTimeSlotModal() {
    document.getElementById('createTimeSlotModal').classList.remove('hidden');
}

function closeCreateTimeSlotModal() {
    document.getElementById('createTimeSlotModal').classList.add('hidden');
    document.getElementById('createTimeSlotForm').reset();
}

// Quick Add Functions
function quickAddCourse() {
    const form = document.getElementById('quickCourseForm');
    const formData = new FormData(form);
    
    // Get selected season from the page
    @if($selectedSeason)
    const selectedSeason = {
        id: {{ $selectedSeason->id }},
        name: '{{ $selectedSeason->name }}',
        start_date: '{{ $selectedSeason->season_start->format('Y-m-d') }}',
        end_date: '{{ $selectedSeason->season_end->format('Y-m-d') }}'
    };
    @else
    const selectedSeason = {
        id: null,
        name: 'No season selected',
        start_date: new Date().toISOString().split('T')[0],
        end_date: new Date().toISOString().split('T')[0]
    };
    @endif
    
    // Generate auto code
    const nextId = Date.now() % 1000;
    const courseData = {
        code: 'CRS-' + String(nextId).padStart(3, '0'),
        title: formData.get('title'),
        max_students: parseInt(formData.get('max_students')),
        hours: parseInt(formData.get('hours')),
        level: 'beginner',
        season_id: selectedSeason.id,
        start_date: selectedSeason.start_date,
        end_date: selectedSeason.end_date
    };
    
    fetch('{{ route("campus.courses.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(courseData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Create draggable course element
            createDraggableCourse(data.course);
            alert('Curs creat correctament. Ara pots arrossegar-lo al calendari.');
            form.reset();
        } else {
            alert('Error: ' + (data.message || 'No s\'ha pogut crear el curs'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de connexió. Si us plau, torna-ho a intentar.');
    });
}

function createDraggableCourse(course) {
    const coursesBox = document.getElementById('coursesBox');
    if (!coursesBox) {
        // Create courses box if it doesn't exist
        const box = document.createElement('div');
        box.id = 'coursesBox';
        box.className = 'bg-yellow-50 border-2 border-dashed border-yellow-300 rounded-lg p-4 mb-6';
        box.innerHTML = '<h3 class="font-semibold mb-2 text-yellow-800">Cursos per Assignar (Arrossega al calendari)</h3><div id="draggableCourses" class="flex flex-wrap gap-2"></div>';
        document.querySelector('.container').insertBefore(box, document.querySelector('.bg-white.rounded-lg.shadow'));
    }
    
    const courseElement = document.createElement('div');
    courseElement.className = 'bg-yellow-100 border border-yellow-300 rounded px-3 py-2 cursor-move text-sm hover:bg-yellow-200';
    courseElement.draggable = true;
    courseElement.dataset.courseId = course.id;
    courseElement.dataset.courseTitle = course.title;
    courseElement.innerHTML = `<strong>${course.code}</strong><br>${course.title}`;
    
    // Add drag events
    courseElement.addEventListener('dragstart', function(e) {
        e.dataTransfer.setData('courseId', course.id);
        e.dataTransfer.setData('courseTitle', course.title);
        this.classList.add('opacity-50');
    });
    
    courseElement.addEventListener('dragend', function() {
        this.classList.remove('opacity-50');
    });
    
    document.getElementById('draggableCourses').appendChild(courseElement);
}

// Drag and Drop functions
function allowDrop(ev) {
    ev.preventDefault();
}

function highlightDropZone(ev) {
    ev.target.classList.add('bg-blue-100', 'border-blue-400');
}

function unhighlightDropZone(ev) {
    ev.target.classList.remove('bg-blue-100', 'border-blue-400');
}

function handleDrop(ev, dayOfWeek, timeSlotId) {
    ev.preventDefault();
    ev.target.classList.remove('bg-blue-100', 'border-blue-400');
    
    const courseId = ev.dataTransfer.getData('courseId');
    const courseTitle = ev.dataTransfer.getData('courseTitle');
    
    if (!courseId) {
        alert('No s\'ha pogut obtenir el curs');
        return;
    }
    
    // Open assign modal with pre-filled data
    openAssignModal(dayOfWeek, timeSlotId);
    
    // Pre-select the course
    setTimeout(() => {
        const courseSelect = document.querySelector('select[name="course_id"]');
        if (courseSelect) {
            courseSelect.value = courseId;
        }
    }, 100);
    
    // Remove the draggable course element
    const draggableElement = document.querySelector(`[data-course-id="${courseId}"]`);
    if (draggableElement) {
        draggableElement.remove();
    }
}

function quickAddSpace() {
    const form = document.getElementById('quickSpaceForm');
    const formData = new FormData(form);
    
    fetch('{{ route("campus.resources.spaces") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            code: formData.get('code'),
            name: formData.get('name'),
            capacity: formData.get('capacity'),
            type: formData.get('type')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Espai creat correctament');
            form.reset();
            location.reload();
        } else {
            alert('Error creant espai');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creant espai');
    });
}

function quickAddTimeSlot() {
    const form = document.getElementById('quickTimeSlotForm');
    const formData = new FormData(form);
    
    const timeData = {
        'M11': { start: '11:00:00', end: '12:30:00', desc: 'Matí 11:00-12:30' },
        'T16': { start: '16:00:00', end: '17:30:00', desc: 'Tarda 16:00-17:30' },
        'T18': { start: '18:00:00', end: '19:30:00', desc: 'Tarda 18:00-19:30' }
    };
    
    const selectedTime = timeData[formData.get('code')];
    
    fetch('{{ route("campus.resources.timeslots") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            day_of_week: formData.get('day_of_week'),
            code: formData.get('code'),
            start_time: selectedTime.start,
            end_time: selectedTime.end,
            description: selectedTime.desc
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Franja creada correctament');
            form.reset();
            location.reload();
        } else {
            alert('Error creant franja');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creant franja');
    });
}

function filterBySeason() {
    const seasonSelect = document.getElementById('semesterSelect');
    const selectedSeason = seasonSelect ? seasonSelect.value : null;
    
    console.log('Selected season:', selectedSeason); // Debug
    
    const url = new URL(window.location);
    
    if (selectedSeason && selectedSeason !== '') {
        url.searchParams.set('season', selectedSeason);
    } else {
        url.searchParams.delete('season');
    }
    
    console.log('Navigating to:', url.toString()); // Debug
    window.location.href = url.toString();
}
</script>

<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Re-Cursos</h1>
            @if($selectedSeason)
                <p class="text-sm text-gray-600 mt-1">
                    Temporada: <strong>{{ $selectedSeason->name ?? 'Planning' }}</strong> ({{ $coursesCount }} cursos)
                </p>
            @else
                <p class="text-sm text-gray-600 mt-1">
                    No hay temporada en planning
                </p>
            @endif
        </div>
        <div class="flex gap-4">
            <button onclick="openQuickAddCourse()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Crear Curs
            </button>
            <button onclick="openSpaceModal()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                <i class="fas fa-door-open mr-2"></i>Afegir Espai
            </button>
            <button onclick="openTimeSlotModal()" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">
                <i class="fas fa-clock mr-2"></i>Afegir Franja
            </button>
        </div>
    </div>

    <!-- Quick Add Forms -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="space-y-4">
            <!-- Selector de Temporada -->
            <div class="flex items-center gap-2">
                <h4 class="font-semibold">Temporada:</h4>
                <select id="seasonFilter" class="border rounded px-2 py-1 text-sm">
                    @if($selectedSeason)
                        <option value="{{ $selectedSeason->id }}" selected>{{ $selectedSeason->name }}</option>
                    @endif
                </select>
            </div>
            
            <!-- Cursos Disponibles -->
            @if($selectedSeason)
            <div class="border rounded p-3">
                <h5 class="text-sm font-semibold mb-2">Cursos Disponibles ({{ $coursesCount }}):</h5>
                <div class="flex flex-wrap gap-1">
                    @php
                        $courses = \App\Models\CampusCourse::where('season_id', $selectedSeason->id)->get();
                    @endphp
                    @foreach($courses as $course)
                        <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs cursor-pointer hover:bg-blue-200" 
                             title="{{ $course->title }}"
                             onclick="showAssignForm('{{ $course->id }}', '{{ $course->title }}', '{{ $course->code ?? substr($course->title, 0, 3) }}')">
                            {{ Str::limit($course->code ?? substr($course->title, 0, 3), 8) }}
                        </div>
                    @endforeach
                </div>
                
                <!-- Formulario de Asignación Oculto -->
                <div id="assignForm" class="hidden mt-4 border rounded p-3 bg-gray-50">
                    <h6 class="text-sm font-semibold mb-2">Asignar Curs: <span id="selectedCourseName" class="text-blue-600"></span></h6>
                    <form id="assignCourseForm" class="space-y-2">
                        <input type="hidden" id="courseId" name="course_id">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" id="seasonId" name="season_id">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Títol:</label>
                                <input type="text" id="courseTitle" name="title" class="w-full border rounded px-2 py-1 text-sm" required>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Codi:</label>
                                <input type="text" id="courseCode" name="code" class="w-full border rounded px-2 py-1 text-sm" readonly>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Descripció:</label>
                                <textarea id="description" name="description" rows="2" class="w-full border rounded px-2 py-1 text-sm"></textarea>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Nivell:</label>
                                <select id="level" name="level" class="w-full border rounded px-2 py-1 text-sm">
                                    <option value="">Seleccionar nivell...</option>
                                    <option value="beginner">Principiant</option>
                                    <option value="intermediate">Intermedi</option>
                                    <option value="advanced">Avançat</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Max. Alumnes:</label>
                                <input type="number" id="maxStudents" name="max_students" class="w-full border rounded px-2 py-1 text-sm" min="1">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Crèdits:</label>
                                <input type="number" id="credits" name="credits" class="w-full border rounded px-2 py-1 text-sm" min="0">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Ubicació:</label>
                                <input type="text" id="location" name="location" class="w-full border rounded px-2 py-1 text-sm">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Format:</label>
                                <select id="format" name="format" class="w-full border rounded px-2 py-1 text-sm">
                                    <option value="">Seleccionar format...</option>
                                    <option value="Presencial">Presencial</option>
                                    <option value="Online">Online</option>
                                    <option value="Híbrid">Híbrid</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Data Inici:</label>
                                <input type="date" id="startDate" name="start_date" class="w-full border rounded px-2 py-1 text-sm" 
                                       value="{{ $selectedSeason ? $selectedSeason->season_start->format('Y-m-d') : '' }}" readonly>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Data Fi:</label>
                                <input type="date" id="endDate" name="end_date" class="w-full border rounded px-2 py-1 text-sm" 
                                       value="{{ $selectedSeason ? $selectedSeason->season_end->format('Y-m-d') : '' }}" readonly>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Requeriments:</label>
                                <textarea id="requirements" name="requirements" rows="2" class="w-full border rounded px-2 py-1 text-sm"></textarea>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Objectius:</label>
                                <textarea id="objectives" name="objectives" rows="2" class="w-full border rounded px-2 py-1 text-sm"></textarea>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Espai:</label>
                                <select id="spaceSelect" name="space_id" class="w-full border rounded px-2 py-1 text-sm">
                                    <option value="">Seleccionar espai...</option>
                                    @foreach($spaces as $space)
                                        <option value="{{ $space->id }}">{{ $space->code }} - {{ $space->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-700">Franja:</label>
                                <select id="timeSlotSelect" name="time_slot_id" class="w-full border rounded px-2 py-1 text-sm">
                                    <option value="">Seleccionar franja...</option>
                                    @foreach($timeSlots as $timeSlot)
                                        <option value="{{ $timeSlot->id }}">{{ $timeSlot->day_name }} {{ $timeSlot->start_time }}-{{ $timeSlot->end_time }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-700">Semestre:</label>
                                <select name="semester" class="w-full border rounded px-2 py-1 text-sm">
                                    <option value="1Q">1r Quadrimestre</option>
                                    <option value="2Q">2n Quadrimestre</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Sessions:</label>
                                <input type="number" id="sessions" name="sessions" class="w-full border rounded px-2 py-1 text-sm" min="1" max="100" title="Nombre de sessions">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Hores totals:</label>
                                <input type="number" id="hours" name="hours" class="w-full border rounded px-2 py-1 text-sm" min="0">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Preu (€):</label>
                                <input type="number" id="price" name="price" class="w-full border rounded px-2 py-1 text-sm" min="0" step="0.01">
                            </div>
                        </div>
                        
                        <div class="flex gap-2 mt-3">
                            <button type="button" onclick="assignCourse()" class="bg-blue-500 text-white px-3 py-1 text-sm rounded hover:bg-blue-600">
                                <i class="fas fa-check"></i> Actualitzar
                            </button>
                            <button type="button" onclick="hideAssignForm()" class="bg-gray-300 text-gray-700 px-3 py-1 text-sm rounded hover:bg-gray-400">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            
            <!-- Formulario de Creación -->
            <div class="flex items-center gap-2">
                <h4 class="font-semibold">Afegir Curs Ràpid:</h4>
                <form id="quickCourseForm" class="flex gap-2 items-center">
                    <input type="text" name="title" placeholder="Títol del curs" class="border rounded px-2 py-1 text-sm" required>
                    <input type="number" name="max_students" value="25" placeholder="Max. alumnes" class="border rounded px-2 py-1 text-sm w-20" title="Màxim d'alumnes per curs">
                    <input type="number" name="hours" value="18" placeholder="Hores" class="border rounded px-2 py-1 text-sm w-16" title="Hores totals del curs">
                    <button type="button" onclick="quickAddCourse()" class="bg-green-500 text-white px-3 py-1 text-sm rounded hover:bg-green-600">
                        <i class="fas fa-plus"></i> Afegir
                    </button>
                </form>
            </div>
        </div>
    </div>

            <!-- Add Space -->
            {{-- <div class="border rounded p-3">
                <h4 class="font-semibold mb-2">Afegir Espai</h4>
                <form id="quickSpaceForm" class="space-y-2">
                    <input type="text" name="code" placeholder="Codi (SA, AM1...)" class="w-full border rounded px-2 py-1 text-sm">
                    <input type="text" name="name" placeholder="Nom" class="w-full border rounded px-2 py-1 text-sm">
                    <input type="number" name="capacity" placeholder="Capacitat" class="w-full border rounded px-2 py-1 text-sm">
                    <select name="type" class="w-full border rounded px-2 py-1 text-sm">
                        <option value="">Tipus...</option>
                        @foreach(\App\Models\CampusSpace::TYPES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="quickAddSpace()" class="w-full bg-purple-500 text-white px-2 py-1 text-sm rounded hover:bg-purple-600">
                        <i class="fas fa-plus"></i> Afegir
                    </button>
                </form>
            </div> --}}
            
            <!-- Add Time Slot -->
            {{-- <div class="border rounded p-3">
                <h4 class="font-semibold mb-2">Afegir Franja Horària</h4>
                <form id="quickTimeSlotForm" class="space-y-2">
                    <select name="day_of_week" class="w-full border rounded px-2 py-1 text-sm">
                        <option value="">Dia...</option>
                        @foreach(\App\Models\CampusTimeSlot::DAYS as $num => $day)
                            <option value="{{ $num }}">{{ $day }}</option>
                        @endforeach
                    </select>
                    <select name="code" class="w-full border rounded px-2 py-1 text-sm">
                        <option value="">Franja...</option>
                        <option value="M11">Matí 11:00-12:30</option>
                        <option value="T16">Tarda 16:00-17:30</option>
                        <option value="T18">Tarda 18:00-19:30</option>
                    </select>
                    <button type="button" onclick="quickAddTimeSlot()" class="w-full bg-orange-500 text-white px-2 py-1 text-sm rounded hover:bg-orange-600">
                        <i class="fas fa-plus"></i> Afegir
                    </button>
                </form>
            </div> --}}   

    <!-- Calendar Grid -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="grid grid-cols-6 bg-gray-50">
            <div class="border p-3 font-semibold text-center">Hora/Dia</div>
            @foreach([1 => 'Dilluns', 2 => 'Dimarts', 3 => 'Dimecres', 4 => 'Dijous', 5 => 'Divendres'] as $day => $dayName)
                <div class="border p-3 font-semibold text-center">{{ $dayName }}</div>
            @endforeach
        </div>

        @foreach($timeSlots->groupBy('code') as $timeCode => $slots)
            <div class="grid grid-cols-6 border-t">
                <div class="border p-3 font-medium bg-gray-50">
                    {{ $slots->first()->formatted_time }}
                </div>
                
                @foreach([1, 2, 3, 4, 5] as $dayOfWeek)
                    <div class="border p-2 min-h-[80px] bg-white hover:bg-gray-50"
                         ondrop="handleDrop(event, {{ $dayOfWeek }}, {{ $slot->id ?? 0 }})"
                         ondragover="allowDrop(event)"
                         ondragenter="highlightDropZone(event)"
                         ondragleave="unhighlightDropZone(event)">
                        @php
                            $slot = $slots->firstWhere('day_of_week', $dayOfWeek);
                            $course = $slot ? $slot->courses->first() : null;
                        @endphp
                        
                        @if($course)
                            <div class="p-2 rounded text-xs {{ 
                                $course->status === 'planning' ? 'bg-blue-100 text-blue-800' : 
                                ($course->status === 'in_progress' ? 'bg-green-100 text-green-800' : 
                                ($course->status === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800'))
                            }}">
                                <div class="font-semibold">{{ $course->title }}</div>
                                <div>{{ $course->code }}</div>
                                <div>{{ $course->space->code ?? 'Sense espai' }}</div>
                                <div>{{ $course->mainTeacher()?->first_name ?? 'Sense professor' }}</div>
                                @if($course->status === 'planning')
                                    <div class="text-blue-600 mt-1">📋 Planificació</div>
                                @endif
                            </div>
                        @else
                            <div class="text-gray-400 text-xs h-full flex items-center justify-center">
                                <span class="text-lg">+</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Spaces Summary -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Resum Espais</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($spaces->groupBy('type') as $type => $typeSpaces)
                <div class="border rounded p-4">
                    <h3 class="font-semibold mb-2">{{ \App\Models\CampusSpace::TYPES[$type] }}</h3>
                    @foreach($typeSpaces as $space)
                        @php
                            // Check if space has assigned courses
                            $hasCourses = \App\Models\CampusCourse::where('space_id', $space->id)
                                ->whereNotNull('time_slot_id')
                                ->exists();
                        @endphp
                        <div class="text-sm text-gray-600 flex items-center justify-between">
                            <span>{{ $space->code }} - {{ $space->formatted_capacity }}</span>
                            @if($hasCourses)
                                <span class="text-green-500" title="Disponible con cursos asignados">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            @else
                                <span class="text-gray-400" title="Disponible sin cursos asignados">
                                    <i class="far fa-circle"></i>
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Quick Status -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Resum Cursos</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ App\Models\CampusCourse::count() }}</div>
                <div class="text-sm text-gray-600 mt-1">Cursos totals</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ App\Models\CampusSpace::count() }}</div>
                <div class="text-sm text-gray-600 mt-1">Espais totals</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-purple-600">{{ App\Models\CampusTimeSlot::count() }}</div>
                <div class="text-sm text-gray-600 mt-1">Franjes totals</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-orange-600">{{ \App\Models\CampusCourseSchedule::where('semester', $semester)->count() }}</div>
                <div class="text-sm text-gray-600 mt-1">Assignacions {{ $semester }}</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Assign form exists, add event listener
    const assignForm = document.getElementById('assignForm');
    if (assignForm) {
        // Initially hidden
        assignForm.classList.add('hidden');
    }
});

function showAssignForm(courseId, courseTitle, courseCode) {
    const form = document.getElementById('assignForm');
    const courseIdField = document.getElementById('courseId');
    const courseNameSpan = document.getElementById('selectedCourseName');
    const titleField = document.getElementById('courseTitle');
    const codeField = document.getElementById('courseCode');
    const seasonIdField = document.getElementById('seasonId');
    const maxStudentsField = document.getElementById('maxStudents');
    const hoursField = document.getElementById('hours');
    const priceField = document.getElementById('price');
    const spaceSelect = document.getElementById('spaceSelect');
    const timeSlotSelect = document.getElementById('timeSlotSelect');
    const descriptionField = document.getElementById('description');
    const levelField = document.getElementById('level');
    const creditsField = document.getElementById('credits');
    const locationField = document.getElementById('location');
    const formatField = document.getElementById('format');
    const sessionsField = document.getElementById('sessions');
    const startDateField = document.getElementById('startDate');
    const endDateField = document.getElementById('endDate');
    const requirementsField = document.getElementById('requirements');
    const objectivesField = document.getElementById('objectives');
    
    // Set basic course data
    courseIdField.value = courseId;
    courseNameSpan.textContent = courseTitle + ' (' + courseCode + ')';
    titleField.value = courseTitle;
    codeField.value = courseCode;
    
    // Set season ID from current selected season
    @if($selectedSeason)
    seasonIdField.value = {{ $selectedSeason->id }};
    @endif
    
    // Fetch complete course data to populate all fields
    console.log('Fetching course data for ID:', courseId);
    
    fetch(`/campus/courses/${courseId}/data`)
        .then(response => response.json())
        .then(data => {
            console.log('Received data:', data);
            
            if (data.course) {
                const course = data.course;
                console.log('Course object:', course);
                
                // Basic fields
                maxStudentsField.value = course.max_students || '';
                hoursField.value = course.hours || '';
                priceField.value = course.price || '';
                sessionsField.value = course.sessions || '';
                
                // Additional fields
                descriptionField.value = course.description || '';
                levelField.value = course.level || '';
                creditsField.value = course.credits || '';
                locationField.value = course.location || '';
                formatField.value = course.format || '';
                
                // Format dates properly for date input
                if (course.start_date) {
                    startDateField.value = course.start_date.split('T')[0];
                    console.log('Start date set to:', startDateField.value);
                }
                if (course.end_date) {
                    endDateField.value = course.end_date.split('T')[0];
                    console.log('End date set to:', endDateField.value);
                }
                
                // Handle fields (both requirements and objectives as strings)
                try {
                    // Handle requirements field (stored as JSON string in DB)
                    if (course.requirements) {
                        if (typeof course.requirements === 'string') {
                            // Try to parse as JSON first
                            try {
                                const parsed = JSON.parse(course.requirements);
                                if (Array.isArray(parsed)) {
                                    // If it's an array, join with commas
                                    requirementsField.value = parsed.join(', ');
                                } else {
                                    // If it's a string, use as-is
                                    requirementsField.value = parsed;
                                }
                            } catch (e) {
                                // If not valid JSON, use as string
                                requirementsField.value = course.requirements;
                            }
                        } else if (Array.isArray(course.requirements)) {
                            // If it's already an array, join with commas
                            requirementsField.value = course.requirements.join(', ');
                        } else {
                            // Fallback to string
                            requirementsField.value = course.requirements.toString();
                        }
                    } else {
                        requirementsField.value = '';
                    }
                    
                    // Handle objectives field (now as string)
                    if (course.objectives) {
                        if (typeof course.objectives === 'string') {
                            // Try to parse as JSON first (for backward compatibility)
                            try {
                                const parsed = JSON.parse(course.objectives);
                                if (Array.isArray(parsed)) {
                                    // If it's an array, join with newlines
                                    objectivesField.value = parsed.join('\n');
                                } else {
                                    // If it's a string, use as-is
                                    objectivesField.value = parsed;
                                }
                            } catch (e) {
                                // If not valid JSON, use as string
                                objectivesField.value = course.objectives;
                            }
                        } else if (Array.isArray(course.objectives)) {
                            // If it's already an array, join with newlines
                            objectivesField.value = course.objectives.join('\n');
                        } else {
                            // Fallback to string
                            objectivesField.value = course.objectives.toString();
                        }
                    } else {
                        objectivesField.value = '';
                    }
                } catch (e) {
                    console.error('Error parsing fields:', e);
                    console.log('Requirements raw value:', course.requirements);
                    console.log('Objectives raw value:', course.objectives);
                    requirementsField.value = course.requirements || '';
                    objectivesField.value = course.objectives || '';
                }
                
                // Load schedule data if available
                if (course.space_id) {
                    spaceSelect.value = course.space_id;
                    console.log('Space set to:', course.space_id);
                }
                if (course.time_slot_id) {
                    timeSlotSelect.value = course.time_slot_id;
                    console.log('Time slot set to:', course.time_slot_id);
                }
                
                // Find semester select and set value (if needed)
                const semesterSelect = document.getElementById('semesterSelect');
                if (semesterSelect && course.semester) {
                    semesterSelect.value = course.semester;
                }
                
                console.log('Schedule loaded:', {
                    space_id: course.space_id,
                    time_slot_id: course.time_slot_id,
                    semester: course.semester
                });
            } else {
                console.error('No course data received');
            }
        })
        .catch(error => {
            console.error('Error fetching course data:', error);
            alert('Error carregant les dades del curs: ' + error.message);
        });
    
    // Show form
    form.classList.remove('hidden');
    
    // Scroll to form
    form.scrollIntoView({ behavior: 'smooth' });
}

function hideAssignForm() {
    const form = document.getElementById('assignForm');
    const courseIdField = document.getElementById('courseId');
    const courseNameSpan = document.getElementById('selectedCourseName');
    const titleField = document.getElementById('courseTitle');
    const codeField = document.getElementById('courseCode');
    const seasonIdField = document.getElementById('seasonId');
    const maxStudentsField = document.getElementById('maxStudents');
    const hoursField = document.getElementById('hours');
    const priceField = document.getElementById('price');
    const spaceSelect = document.getElementById('spaceSelect');
    const timeSlotSelect = document.getElementById('timeSlotSelect');
    const descriptionField = document.getElementById('description');
    const levelField = document.getElementById('level');
    const creditsField = document.getElementById('credits');
    const locationField = document.getElementById('location');
    const formatField = document.getElementById('format');
    const sessionsField = document.getElementById('sessions');
    const startDateField = document.getElementById('startDate');
    const endDateField = document.getElementById('endDate');
    const requirementsField = document.getElementById('requirements');
    const objectivesField = document.getElementById('objectives');
    
    // Clear all form fields
    courseIdField.value = '';
    courseNameSpan.textContent = '';
    titleField.value = '';
    codeField.value = '';
    seasonIdField.value = '';
    maxStudentsField.value = '';
    hoursField.value = '';
    priceField.value = '';
    sessionsField.value = '';
    descriptionField.value = '';
    levelField.value = '';
    creditsField.value = '';
    locationField.value = '';
    formatField.value = '';
    startDateField.value = '';
    endDateField.value = '';
    requirementsField.value = '';
    objectivesField.value = '';
    spaceSelect.value = '';
    timeSlotSelect.value = '';
    
    // Hide form
    form.classList.add('hidden');
}

function assignCourse() {
    const form = document.getElementById('assignCourseForm');
    if (!form) {
        console.error('Assign form not found');
        return;
    }
    
    const courseId = document.getElementById('courseId').value;
    const spaceId = document.getElementById('spaceSelect').value;
    const timeSlotId = document.getElementById('timeSlotSelect').value;
    
    if (!courseId || !spaceId || !timeSlotId) {
        alert('Por favor, selecciona espai y franja horària');
        return;
    }
    
    // Create form data
    const formData = new FormData(form);
    
    // Debug: Mostrar datos que se enviarán
    console.log('=== PUT REQUEST DEBUG ===');
    console.log('Course ID:', courseId);
    console.log('Space ID:', spaceId);
    console.log('Time Slot ID:', timeSlotId);
    console.log('Form data entries:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}:`, value);
    }
    
    // Send AJAX request to UPDATE course using method spoofing
    const formDataForPut = new FormData();
    
    // Check for conflicts before sending
    if (spaceSelect.value && timeSlotSelect.value) {
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Verificant coincidències...';
        }
        
        // Check conflicts via AJAX
        fetch(`/campus/courses/check-conflict`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                space_id: spaceSelect.value,
                time_slot_id: timeSlotSelect.value,
                exclude_course_id: courseId
            })
        })
        .then(response => response.json())
        .then(conflictData => {
            // Re-enable submit button
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Assignar';
            }
            
            if (conflictData.conflict) {
                // Show conflict details
                let message = 'Coincidència detectada:\n\n';
                if (conflictData.conflicts && conflictData.conflicts.length > 0) {
                    conflictData.conflicts.forEach(conflict => {
                        message += `• ${conflict.title} (${conflict.code})\n`;
                    });
                }
                message += '\nEl espai i franja ja estan ocupats.';
                alert(message);
                return;
            }
            
            // No conflicts, proceed with update
            submitUpdate();
        })
        .catch(error => {
            // Re-enable submit button
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Assignar';
            }
            console.error('Conflict check failed:', error);
            // Proceed anyway if conflict check fails
            submitUpdate();
        });
    } else {
        submitUpdate();
    }
    
    function submitUpdate() {
        // Add all form fields
        for (let [key, value] of formData.entries()) {
            if (key !== '_method') {  // Skip the old _method if exists
                formDataForPut.append(key, value);
            }
        }
        
        // Add _method override as last field (Laravel requirement)
        formDataForPut.append('_method', 'PUT');
        
        fetch(`/campus/courses/${courseId}`, {
            method: 'POST',  // Always use POST with method spoofing
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formDataForPut
        })
        .then(response => {
            console.log('PUT Response status:', response.status);
            console.log('PUT Response headers:', response.headers);
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('PUT Error response:', text);
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('PUT Response data:', data);
            
            if (data.success) {
                alert('Curs actualizat correctament!');
                hideAssignForm();
                // Reload page to show updated calendar
                window.location.reload();
            } else {
                console.error('PUT Error details:', data);
                
                if (data.conflict) {
                    // Show conflict details
                    let message = 'Coincidencia detectada:\n\n';
                    if (data.conflicts && data.conflicts.length > 0) {
                        data.conflicts.forEach(conflict => {
                            message += `• ${conflict.title} (${conflict.code})\n`;
                        });
                    }
                    message += '\nEl espacio y franja ya están ocupados.';
                    alert(message);
                } else {
                    alert('Error: ' + (data.message || 'No s\'ha pogut actualitzar el curs'));
                }
            }
        })
        .catch(error => {
            console.error('PUT Request failed:', error);
            alert('Error de connexió: ' + error.message);
        });
    }
}

// Semester select exists, add event listener
const semesterSelect = document.getElementById('semesterSelect');
if (semesterSelect) {
    semesterSelect.addEventListener('change', function() {
        window.location.href = '?semester=' + this.value;
    });
}
</script>
</script>

<!-- Assign Modal -->
<div id="assignModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Assignar Curs</h3>
        <form id="assignForm">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Curs</label>
                <select name="course_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Seleccionar curs...</option>
                    @foreach(App\Models\CampusCourse::active()->get() as $course)
                        <option value="{{ $course->id }}">{{ $course->title }} ({{ $course->max_students }} alumnes)</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Espai</label>
                <select name="space_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Seleccionar espai...</option>
                    @foreach($spaces as $space)
                        <option value="{{ $space->id }}">{{ $space->code }} - {{ $space->formatted_capacity }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Franja Horària</label>
                <select name="time_slot_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Seleccionar franja...</option>
                    @foreach($timeSlots as $slot)
                        <option value="{{ $slot->id }}">{{ $slot->full_description }}</option>
                    @endforeach
                </select>
            </div>
            
            <input type="hidden" name="semester" value="{{ $semester }}">
            
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Assignar
                </button>
                <button type="button" onclick="closeAssignModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                    Cancel·lar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Create Course Modal -->
<div id="createCourseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Crear Nou Curs</h3>
        <form id="createCourseForm" action="{{ route('campus.courses.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Codi del Curs</label>
                <input type="text" name="code" class="w-full border rounded px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Títol del Curs</label>
                <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Descripció</label>
                <textarea name="description" class="w-full border rounded px-3 py-2" rows="3"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Hores</label>
                    <input type="number" name="hours" class="w-full border rounded px-3 py-2" value="18" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Màxim Alumnes</label>
                    <input type="number" name="max_students" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nivell</label>
                <select name="level" class="w-full border rounded px-3 py-2" required>
                    <option value="beginner">Principiant</option>
                    <option value="intermediate">Intermedi</option>
                    <option value="advanced">Avançat</option>
                    <option value="expert">Expert</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Temporada</label>
                <select name="season_id" class="w-full border rounded px-3 py-2" required>
                    @foreach(App\Models\CampusSeason::where('is_active', true)->get() as $season)
                        <option value="{{ $season->id }}">{{ $season->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Crear Curs
                </button>
                <button type="button" onclick="closeCreateCourseModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                    Cancel·lar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
