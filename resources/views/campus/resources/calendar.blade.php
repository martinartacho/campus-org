@extends('campus.shared.layout')

@section('title', 'Re-Cursos - Calendari')

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
    const semesterSelect = document.getElementById('semesterSelect');
    const currentSemester = semesterSelect ? semesterSelect.value : '1Q';
    
    // Get current season based on semester
    const seasons = {!! App\Models\CampusSeason::where('is_active', true)->get()->map(function($season) {
        return [
            'id' => $season->id,
            'name' => $season->name,
            'start_date' => $season->season_start,
            'end_date' => $season->season_end,
            'is_current' => $season->is_current
        ];
    })->toJson() !!};
    
    let selectedSeason = seasons.find(s => s.is_current);
    if (!selectedSeason) {
        selectedSeason = seasons[0]; // fallback to first active season
    }
    
    // Generate auto code locally to avoid API call
    const nextId = Date.now() % 1000; // Simple fallback
    const courseData = {
        code: 'CRS-' + String(nextId).padStart(3, '0'),
        title: formData.get('title'),
        max_students: 25,
        hours: 18,
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
            alert('Error creant curs: ' + (data.message || 'Error desconegut'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creant curs: ' + (error.message || 'Error de connexió'));
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
</script>

<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Re-Cursos</h1>
        <div class="flex gap-4">
            <button onclick="openCreateCourseModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Crear Curs
            </button>
            <button onclick="openCreateSpaceModal()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                <i class="fas fa-door-open mr-2"></i>Afegir Espai
            </button>
            <button onclick="openCreateTimeSlotModal()" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">
                <i class="fas fa-clock mr-2"></i>Afegir Franja
            </button>
            <select id="semesterSelect" class="border rounded px-3 py-2">
                <option value="1Q" {{ $semester === '1Q' ? 'selected' : '' }}>1r Quadrimestre</option>
                <option value="2Q" {{ $semester === '2Q' ? 'selected' : '' }}>2n Quadrimestre</option>
            </select>
            <button onclick="openAssignModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Assignar Curs
            </button>
        </div>
    </div>

    <!-- Quick Add Forms -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Add Course -->
            <div class="border rounded p-3">
                <h4 class="font-semibold mb-2">Afegir Curs Ràpid</h4>
                <form id="quickCourseForm" class="space-y-2">
                    <input type="hidden" name="max_students" value="25">
                    <input type="hidden" name="hours" value="18">
                    <input type="text" name="title" placeholder="Títol del curs" class="w-full border rounded px-2 py-1 text-sm" required>
                    <button type="button" onclick="quickAddCourse()" class="w-full bg-green-500 text-white px-2 py-1 text-sm rounded hover:bg-green-600">
                        <i class="fas fa-plus"></i> Afegir Curs
                    </button>
                </form>
            </div>
            
            <!-- Add Space -->
            <div class="border rounded p-3">
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
            </div>
            
            <!-- Add Time Slot -->
            <div class="border rounded p-3">
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
            </div>
            
            <!-- Quick Status -->
            <div class="border rounded p-3">
                <h4 class="font-semibold mb-2">Estat</h4>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span>Cursos totals:</span>
                        <span class="font-semibold">{{ App\Models\CampusCourse::count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Espais totals:</span>
                        <span class="font-semibold">{{ App\Models\CampusSpace::count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Franjes totals:</span>
                        <span class="font-semibold">{{ App\Models\CampusTimeSlot::count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Assignacions {{ $semester }}:</span>
                        <span class="font-semibold">{{ \App\Models\CampusCourseSchedule::where('semester', $semester)->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            $schedule = $slot ? $slot->courseSchedules->first() : null;
                        @endphp
                        
                        @if($schedule)
                            <div class="p-2 rounded text-xs {{ 
                                $schedule->status === 'assigned' ? 'bg-green-100 text-green-800' : 
                                ($schedule->status === 'conflict' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') 
                            }}">
                                <div class="font-semibold">{{ $schedule->course->title }}</div>
                                <div>{{ $schedule->space->code }}</div>
                                <div>{{ $schedule->course->mainTeacher()?->first_name ?? 'Sense professor' }}</div>
                                @if($schedule->status === 'conflict')
                                    <div class="text-red-600 mt-1">⚠️ {{ $schedule->notes }}</div>
                                @endif
                            </div>
                        @else
                           <button onclick="openAssignModal({{ $dayOfWeek }}, {{ $slot->id ?? 0 }})"
                                    class="w-full h-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded p-2 text-xs">
                                + Assignar
                            </button>
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
                        <div class="text-sm text-gray-600">
                            {{ $space->code }} - {{ $space->formatted_capacity }}
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Assign form exists, add event listener
    const assignForm = document.getElementById('assignForm');
    if (assignForm) {
        assignForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("campus.resources.assign") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.conflicts.length > 0) {
                        alert('Assignat amb conflictes: ' + data.conflicts.join(', '));
                    } else {
                        alert('Assignat correctament');
                    }
                    location.reload();
                } else {
                    alert('Error en l\'assignació');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error en l\'assignació');
            });
        });
    }

    // Semester select exists, add event listener
    const semesterSelect = document.getElementById('semesterSelect');
    if (semesterSelect) {
        semesterSelect.addEventListener('change', function() {
            window.location.href = '?semester=' + this.value;
        });
    }
});
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
