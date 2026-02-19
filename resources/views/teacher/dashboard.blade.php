<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Teacher
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- REUTILITZEM el dashboard real --}}
            <x-dashboard.teacher
                :teacher="$teacher"
                :season="$season"
                :seasons="$seasons"
                :teacher-courses="$teacherCourses"
                :stats="$stats"
                :consentments="$consentments"
                :current-season="$currentSeason"
                :debug="$debug"
                :error="$error"
            />

        </div>
    </div>
</x-app-layout>
