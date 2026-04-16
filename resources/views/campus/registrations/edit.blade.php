@extends('campus.shared.layout')

@section('title', __('campus.edit_registration'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.registrations.index') }}"
               class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.registrations') }}
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.registrations.show', $registration->id) }}"
               class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ $registration->registration_code }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.edit') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">
                {{ __('campus.edit_registration') }}
            </h2>
            <p class="text-gray-600 mt-1">
                {{ __('campus.registration_code') }}: <span class="font-mono">{{ $registration->registration_code }}</span>
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('campus.registrations.show', $registration->id) }}" 
               class="text-gray-600 hover:text-gray-900">
                <i class="bi bi-eye mr-2"></i>{{ __('campus.view') }}
            </a>
            <a href="{{ route('campus.registrations.index') }}" 
               class="text-gray-600 hover:text-gray-900">
                <i class="bi bi-arrow-left mr-2"></i>{{ __('campus.back_to_list') }}
            </a>
        </div>
    </div>

    <form action="{{ route('campus.registrations.update', $registration->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Student Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.student') }} <span class="text-red-500">*</span>
                </label>
                <!-- Custom Searchable Select for Students -->
                <div class="relative">
                    <input type="hidden" id="student_id" name="student_id" value="{{ $registration->student_id }}" required>
                    <input type="text" id="student_search" 
                           placeholder="{{ __('campus.search_student_placeholder') }}"
                           value="{{ $registration->student->last_name ?? '' }}, {{ $registration->student->first_name ?? '' }}"
                           class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           autocomplete="off">
                    <div id="student_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                        <!-- Options will be populated by JavaScript -->
                    </div>
                </div>
                @error('student_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Course Selection -->
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.course') }} <span class="text-red-500">*</span>
                </label>
                <!-- Custom Searchable Select for Courses -->
                <div class="relative">
                    <input type="hidden" id="course_id" name="course_id" value="{{ $registration->course_id }}" required>
                    <input type="text" id="course_search" 
                           placeholder="{{ __('campus.search_course_placeholder') }}"
                           value="{{ $registration->course->title ?? '' }}"
                           class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           autocomplete="off">
                    <div id="course_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                        <!-- Options will be populated by JavaScript -->
                    </div>
                </div>
                @error('course_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Hidden data for JavaScript -->
        <script>
            window.studentsData = @json($students);
            window.coursesData = @json($courses);
            
            console.log('Students loaded:', window.studentsData.length);
            console.log('Courses loaded:', window.coursesData.length);
            
            // Pre-select current values
            window.currentStudentId = {{ $registration->student_id }};
            window.currentCourseId = {{ $registration->course_id }};
            
            // Values are pre-selected directly in HTML, no need for JavaScript pre-selection
            
            // Simple search functionality for both students and courses
            document.addEventListener('DOMContentLoaded', function() {
                // Student search
                const studentSearch = document.getElementById('student_search');
                const studentDropdown = document.getElementById('student_dropdown');
                const studentId = document.getElementById('student_id');
                
                if (studentSearch && studentDropdown && studentId) {
                    console.log('Student search elements found');
                    
                    studentSearch.addEventListener('focus', function() {
                        showAllStudents();
                    });
                    
                    // Also show on click for better UX
                    studentSearch.addEventListener('click', function() {
                        showAllStudents();
                    });
                    
                    studentSearch.addEventListener('input', function() {
                        filterStudents(this.value);
                    });
                    
                    function showAllStudents() {
                        studentDropdown.classList.remove('hidden');
                        studentDropdown.innerHTML = '';
                        
                        window.studentsData.slice(0, 50).forEach(student => { // Limit to 50 for performance
                            const div = createStudentElement(student);
                            studentDropdown.appendChild(div);
                        });
                    }
                    
                    function filterStudents(query) {
                        studentDropdown.innerHTML = '';
                        
                        if (!query) {
                            showAllStudents();
                            return;
                        }
                        
                        const filtered = window.studentsData.filter(student => {
                            const q = query.toLowerCase();
                            return (student.first_name && student.first_name.toLowerCase().includes(q)) ||
                                   (student.last_name && student.last_name.toLowerCase().includes(q)) ||
                                   (student.email && student.email.toLowerCase().includes(q));
                        });
                        
                        if (filtered.length === 0) {
                            studentDropdown.innerHTML = '<div class="px-3 py-2 text-gray-500">No s\'han trobat resultats</div>';
                        } else {
                            filtered.slice(0, 50).forEach(student => { // Limit to 50 for performance
                                const div = createStudentElement(student);
                                studentDropdown.appendChild(div);
                            });
                        }
                    }
                    
                    function createStudentElement(student) {
                        const div = document.createElement('div');
                        div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b';
                        div.textContent = (student.last_name || '') + ', ' + (student.first_name || '') + ' (' + (student.email || 'N/A') + ')';
                        div.onclick = function() {
                            studentId.value = student.id;
                            studentSearch.value = (student.last_name || '') + ', ' + (student.first_name || '');
                            studentDropdown.classList.add('hidden');
                        };
                        return div;
                    }
                }
                
                // Course search
                const courseSearch = document.getElementById('course_search');
                const courseDropdown = document.getElementById('course_dropdown');
                const courseId = document.getElementById('course_id');
                
                if (courseSearch && courseDropdown && courseId) {
                    console.log('Course search elements found');
                    
                    courseSearch.addEventListener('focus', function() {
                        showAllCourses();
                    });
                    
                    courseSearch.addEventListener('input', function() {
                        filterCourses(this.value);
                    });
                    
                    function showAllCourses() {
                        courseDropdown.classList.remove('hidden');
                        courseDropdown.innerHTML = '';
                        
                        window.coursesData.forEach(course => {
                            const div = createCourseElement(course);
                            courseDropdown.appendChild(div);
                        });
                    }
                    
                    function filterCourses(query) {
                        courseDropdown.innerHTML = '';
                        
                        if (!query) {
                            showAllCourses();
                            return;
                        }
                        
                        const filtered = window.coursesData.filter(course => {
                            const q = query.toLowerCase();
                            return (course.title && course.title.toLowerCase().includes(q)) ||
                                   (course.code && course.code.toLowerCase().includes(q)) ||
                                   (course.season && course.season.toLowerCase().includes(q));
                        });
                        
                        if (filtered.length === 0) {
                            courseDropdown.innerHTML = '<div class="px-3 py-2 text-gray-500">No s\'han trobat resultats</div>';
                        } else {
                            filtered.forEach(course => {
                                const div = createCourseElement(course);
                                courseDropdown.appendChild(div);
                            });
                        }
                    }
                    
                    function createCourseElement(course) {
                        const div = document.createElement('div');
                        div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b';
                        div.textContent = (course.title || '') + ' (' + (course.season || 'N/A') + ')';
                        div.onclick = function() {
                            courseId.value = course.id;
                            courseSearch.value = course.title || '';
                            courseDropdown.classList.add('hidden');
                        };
                        return div;
                    }
                }
                
                // Hide dropdowns when clicking outside
                document.addEventListener('click', function(e) {
                    if (studentSearch && !studentSearch.contains(e.target) && !studentDropdown.contains(e.target)) {
                        studentDropdown.classList.add('hidden');
                    }
                    if (courseSearch && !courseSearch.contains(e.target) && !courseDropdown.contains(e.target)) {
                        courseDropdown.classList.add('hidden');
                    }
                });
            });
        </script>

        <!-- Registration Information -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="registration_date" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.registration_date') }} <span class="text-red-500">*</span>
                </label>
                <input type="date" id="registration_date" name="registration_date" 
                       value="{{ old('registration_date', $registration->registration_date->format('Y-m-d')) }}"
                       required
                       class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('registration_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.amount') }} (€) <span class="text-red-500">*</span>
                </label>
                <input type="number" id="amount" name="amount" 
                       value="{{ old('amount', $registration->amount) }}"
                       step="0.01" min="0" required
                       class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="payment_due_date" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.payment_due_date') }}
                </label>
                <input type="date" id="payment_due_date" name="payment_due_date" 
                       value="{{ old('payment_due_date', $registration->payment_due_date?->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('payment_due_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Status Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.registration_status') }} <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status" required
                        class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ old('status', $registration->status) == 'pending' ? 'selected' : '' }}>
                        {{ __('campus.registration_status_pending') }}
                    </option>
                    <option value="confirmed" {{ old('status', $registration->status) == 'confirmed' ? 'selected' : '' }}>
                        {{ __('campus.registration_status_confirmed') }}
                    </option>
                    <option value="cancelled" {{ old('status', $registration->status) == 'cancelled' ? 'selected' : '' }}>
                        {{ __('campus.registration_status_cancelled') }}
                    </option>
                    <option value="completed" {{ old('status', $registration->status) == 'completed' ? 'selected' : '' }}>
                        {{ __('campus.registration_status_completed') }}
                    </option>
                    <option value="failed" {{ old('status', $registration->status) == 'failed' ? 'selected' : '' }}>
                        {{ __('campus.registration_status_failed') }}
                    </option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.payment_status') }} <span class="text-red-500">*</span>
                </label>
                <select id="payment_status" name="payment_status" required
                        class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ old('payment_status', $registration->payment_status) == 'pending' ? 'selected' : '' }}>
                        {{ __('campus.payment_status_pending') }}
                    </option>
                    <option value="paid" {{ old('payment_status', $registration->payment_status) == 'paid' ? 'selected' : '' }}>
                        {{ __('campus.payment_status_paid') }}
                    </option>
                    <option value="partial" {{ old('payment_status', $registration->payment_status) == 'partial' ? 'selected' : '' }}>
                        {{ __('campus.payment_status_partial') }}
                    </option>
                    <option value="cancelled" {{ old('payment_status', $registration->payment_status) == 'cancelled' ? 'selected' : '' }}>
                        {{ __('campus.payment_status_cancelled') }}
                    </option>
                </select>
                @error('payment_status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Payment Method -->
        <div>
            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('campus.payment_method') }} <span class="text-red-500">*</span>
            </label>
            <select id="payment_method" name="payment_method" 
                    class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">{{ __('campus.select_payment_method') }}</option>
                <option value="web" {{ old('payment_method', $registration->payment_method) == 'web' ? 'selected' : '' }}>
                    {{ __('campus.payment_method_web') }}
                </option>
                <option value="presencial" {{ old('payment_method', $registration->payment_method) == 'presencial' ? 'selected' : '' }}>
                    {{ __('campus.payment_method_presencial') }}
                </option>
                <option value="bank_transfer" {{ old('payment_method', $registration->payment_method) == 'bank_transfer' ? 'selected' : '' }}>
                    {{ __('campus.payment_method_bank_transfer') }}
                </option>
                <option value="other" {{ old('payment_method', $registration->payment_method) == 'other' ? 'selected' : '' }}>
                    {{ __('campus.payment_method_other') }}
                </option>
            </select>
            @error('payment_method')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Notes -->
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('campus.notes') }}
            </label>
            <textarea id="notes" name="notes" rows="4"
                      placeholder="{{ __('campus.notes_placeholder') }}"
                      class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $registration->notes) }}</textarea>
            <p class="mt-1 text-sm text-gray-500">{{ __('campus.notes_max_chars', ['max' => 1000]) }}</p>
            @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Current Info Alert -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-info-circle text-blue-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">{{ __('campus.current_registration_info') }}</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>{{ __('campus.created_at') }}: {{ $registration->created_at->format('d/m/Y H:i') }}</p>
                        <p>{{ __('campus.updated_at') }}: {{ $registration->updated_at->format('d/m/Y H:i') }}</p>
                        @if($registration->total_paid > 0)
                        <p>{{ __('campus.total_paid') }}: €{{ number_format($registration->total_paid, 2) }}</p>
                        @endif
                        @if($registration->remaining_amount > 0)
                        <p>{{ __('campus.remaining_amount') }}: €{{ number_format($registration->remaining_amount, 2) }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t">
            <a href="{{ route('campus.registrations.show', $registration->id) }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                {{ __('campus.cancel') }}
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="bi bi-check-circle mr-2"></i>
                {{ __('campus.update_registration') }}
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set payment due date based on registration date
    const registrationDate = document.getElementById('registration_date');
    const paymentDueDate = document.getElementById('payment_due_date');
    
    registrationDate.addEventListener('change', function() {
        if (!paymentDueDate.value) {
            const date = new Date(this.value);
            date.setDate(date.getDate() + 7); // Default 7 days
            paymentDueDate.value = date.toISOString().split('T')[0];
        }
    });

    // Searchable Select Functionality
    class SearchableSelect {
        constructor(inputId, hiddenId, dropdownId, data, searchFields, preselectedId = null) {
            this.input = document.getElementById(inputId);
            this.hidden = document.getElementById(hiddenId);
            this.dropdown = document.getElementById(dropdownId);
            this.data = data;
            this.searchFields = searchFields;
            this.selectedItem = null;
            this.preselectedId = preselectedId;
            
            this.init();
        }
        
        init() {
            // Pre-select item if provided
            if (this.preselectedId) {
                const preselectedItem = this.data.find(item => item.id == this.preselectedId);
                if (preselectedItem) {
                    this.selectItem(preselectedItem);
                }
            }
            
            // Show dropdown on focus
            this.input.addEventListener('focus', () => this.showDropdown());
            
            // Handle input changes
            this.input.addEventListener('input', (e) => this.handleSearch(e.target.value));
            
            // Handle keyboard navigation
            this.input.addEventListener('keydown', (e) => this.handleKeydown(e));
            
            // Hide dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) {
                    this.hideDropdown();
                }
            });
            
            // Handle dropdown item clicks
            this.dropdown.addEventListener('click', (e) => {
                if (e.target.classList.contains('dropdown-item')) {
                    this.selectItem(JSON.parse(e.target.dataset.item));
                }
            });
        }
        
        showDropdown() {
            this.dropdown.classList.remove('hidden');
            if (!this.input.value) {
                this.showAllItems();
            }
        }
        
        hideDropdown() {
            this.dropdown.classList.add('hidden');
        }
        
        handleSearch(query) {
            const filtered = this.data.filter(item => {
                return this.searchFields.some(field => 
                    item[field].toLowerCase().includes(query.toLowerCase())
                );
            });
            
            this.renderItems(filtered);
        }
        
        showAllItems() {
            this.renderItems(this.data);
        }
        
        renderItems(items) {
            this.dropdown.innerHTML = '';
            
            if (items.length === 0) {
                this.dropdown.innerHTML = `
                    <div class="px-3 py-2 text-gray-500 text-sm">
                        No s'han trobat resultats
                    </div>
                `;
                return;
            }
            
            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'dropdown-item px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0';
                div.dataset.item = JSON.stringify(item);
                
                // Custom display for students and courses
                if (item.display) {
                    div.innerHTML = `
                        <div class="font-medium">${item.display}</div>
                        ${item.email ? `<div class="text-xs text-gray-500">${item.email}</div>` : ''}
                        ${item.code ? `<div class="text-xs text-gray-500">Codi: ${item.code}</div>` : ''}
                    `;
                } else {
                    div.textContent = item.name || item.title;
                }
                
                this.dropdown.appendChild(div);
            });
        }
        
        handleKeydown(e) {
            const items = this.dropdown.querySelectorAll('.dropdown-item');
            let currentIndex = -1;
            
            // Find current selected index
            items.forEach((item, index) => {
                if (item.classList.contains('bg-blue-100')) {
                    currentIndex = index;
                }
            });
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    currentIndex = Math.min(currentIndex + 1, items.length - 1);
                    this.highlightItem(items, currentIndex);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    currentIndex = Math.max(currentIndex - 1, 0);
                    this.highlightItem(items, currentIndex);
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (currentIndex >= 0 && items[currentIndex]) {
                        this.selectItem(JSON.parse(items[currentIndex].dataset.item));
                    }
                    break;
                case 'Escape':
                    this.hideDropdown();
                    break;
            }
        }
        
        highlightItem(items, index) {
            items.forEach(item => item.classList.remove('bg-blue-100'));
            if (items[index]) {
                items[index].classList.add('bg-blue-100');
                items[index].scrollIntoView({ block: 'nearest' });
            }
        }
        
        selectItem(item) {
            this.selectedItem = item;
            this.hidden.value = item.id;
            this.input.value = item.display || item.name || item.title;
            this.hideDropdown();
        }
        
        clear() {
            this.selectedItem = null;
            this.hidden.value = '';
            this.input.value = '';
        }
    }
    
    // Initialize searchable selects with preselected values
    const studentSelect = new SearchableSelect(
        'student_search',
        'student_id',
        'student_dropdown',
        window.studentsData || [],
        ['name', 'display', 'dni', 'email'],
        window.currentStudentId
    );
    
    const courseSelect = new SearchableSelect(
        'course_search',
        'course_id',
        'course_dropdown',
        window.coursesData || [],
        ['title', 'display', 'code', 'season'],
        window.currentCourseId
    );
    
    // Handle form submission validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!document.getElementById('student_id').value) {
            e.preventDefault();
            alert('Has de seleccionar un alumne');
            document.getElementById('student_search').focus();
            return false;
        }
        
        if (!document.getElementById('course_id').value) {
            e.preventDefault();
            alert('Has de seleccionar un curs');
            document.getElementById('course_search').focus();
            return false;
        }
    });
});
</script>
@endsection
