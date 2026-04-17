@extends('campus.shared.layout')

@section('title', __('campus.create_registration'))

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
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.create_registration') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">
            {{ __('campus.create_registration') }}
        </h2>
        <a href="{{ route('campus.registrations.index') }}" 
           class="text-gray-600 hover:text-gray-900">
            <i class="bi bi-arrow-left mr-2"></i>{{ __('campus.back_to_list') }}
        </a>
    </div>

    <form action="{{ route('campus.registrations.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Student Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.student') }} <span class="text-red-500">*</span>
                </label>
                <!-- Custom Searchable Select for Students -->
                <div class="relative">
                    <input type="hidden" id="student_id" name="student_id" required>
                    <input type="text" id="student_search" 
                           placeholder="{{ __('campus.search_student_placeholder') }}"
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
                    <input type="hidden" id="course_id" name="course_id" required>
                    <input type="text" id="course_search" 
                           placeholder="{{ __('campus.search_course_placeholder') }}"
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
            console.log('Page loaded successfully');
            
            // Test basic data first
            window.studentsData = @json($students);
            window.coursesData = @json($courses);
            
            console.log('Students loaded:', window.studentsData.length);
            console.log('Courses loaded:', window.coursesData.length);
            
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
                        
                        window.studentsData.forEach(student => { // Show all students
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
                            filtered.forEach(student => { // Show all filtered results
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
                       value="{{ old('registration_date', now()->format('Y-m-d')) }}"
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
                       value="{{ old('amount') }}"
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
                       value="{{ old('payment_due_date') }}"
                       class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('payment_due_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Payment Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.payment_status') }} <span class="text-red-500">*</span>
                </label>
                <select id="payment_status" name="payment_status" required
                        class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending">{{ __('campus.payment_status_pending') }}</option>
                    <option value="paid">{{ __('campus.payment_status_paid') }}</option>
                    <option value="partial">{{ __('campus.payment_status_partial') }}</option>
                    <option value="cancelled">{{ __('campus.payment_status_cancelled') }}</option>
                </select>
                @error('payment_status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('campus.payment_method') }} <span class="text-red-500">*</span>
                </label>
                <select id="payment_method" name="payment_method" 
                        class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('campus.select_payment_method') }}</option>
                    <option value="web" {{ old('payment_method') == 'web' ? 'selected' : '' }}>
                        {{ __('campus.payment_method_web') }}
                    </option>
                    <option value="presencial" {{ old('payment_method') == 'presencial' ? 'selected' : '' }}>
                        {{ __('campus.payment_method_presencial') }}
                    </option>
                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>
                        {{ __('campus.payment_method_bank_transfer') }}
                    </option>
                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>
                        {{ __('campus.payment_method_other') }}
                    </option>
                </select>
                @error('payment_method')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Notes -->
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('campus.notes') }}
            </label>
            <textarea id="notes" name="notes" rows="4"
                      placeholder="{{ __('campus.notes_placeholder') }}"
                      class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            <p class="mt-1 text-sm text-gray-500">{{ __('campus.notes_max_chars', ['max' => 1000]) }}</p>
            @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t">
            <a href="{{ route('campus.registrations.index') }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                {{ __('campus.cancel') }}
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="bi bi-plus-circle mr-2"></i>
                {{ __('campus.create_registration') }}
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
        constructor(inputId, hiddenId, dropdownId, data, searchFields) {
            this.input = document.getElementById(inputId);
            this.hidden = document.getElementById(hiddenId);
            this.dropdown = document.getElementById(dropdownId);
            this.data = data;
            this.searchFields = searchFields;
            this.selectedItem = null;
            
            this.init();
        }
        
        init() {
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
    
    // Initialize searchable selects
    const studentSelect = new SearchableSelect(
        'student_search',
        'student_id',
        'student_dropdown',
        window.studentsData || [],
        ['name', 'display', 'dni', 'email']
    );
    
    const courseSelect = new SearchableSelect(
        'course_search',
        'course_id',
        'course_dropdown',
        window.coursesData || [],
        ['title', 'display', 'code', 'season']
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
