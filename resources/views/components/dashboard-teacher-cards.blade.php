{{-- resources/views/components/dashboard-teacherscards.blade.php --}}
@auth
    @php
        $user = Auth::user();
        // Verificar si el usuario tiene permisos de teacher
        $hasTeacherAccess = 
                          $user->canany(['events.view', 'event_types.view', 'event_questions.view', 'event_answers.view']) ||
                          $user->can('campus.profile.view') ||
                          $user->can('campus.profile.edit') ||
                          $user->can('campus.my_courses.view') ||
                          $user->can('campus.my_courses.manage') ||
                          $user->can('campus.students.view') ||  // Veure estudiants dels seus cursos
                          $user->can('campus.registrations.view') ||  // Veure matriculacions dels seus cursos
                          $user->can('notifications.view') ||
                          $user->can('notifications.create');

    @endphp
       @if($hasTeacherAccess)
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="bi bi-shield-check me-2"></i>
                    {{ __('gestió del professor') }}
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Las cards estadísticas se muestran en la vista principal -->
            </div>
            
        </div>
        @endif

@endauth