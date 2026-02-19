{{-- resources\views\dashboard.blade.php --}}
@php
    $context = $context ?? session('context');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('site.Dashboard') }} 
        </h2>
    </x-slot>

    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 1. Dashboard Admin --}}
            @if(auth()->user()->hasAnyRole(['admin', 'super-admin']))
                <x-dashboard.admin :stats="$stats ?? []" />

            {{-- 2. Dashboard Manager --}}
            @elseif(auth()->user()->hasAnyRole(['gestor', 'treasury', 'editor', 'manager']))
                <x-dashboard.manager :stats="$stats ?? []" />

            {{-- 3. Teacher --}}
            @elseif(auth()->user()->hasRole('teacher'))
                <x-dashboard.teacher
                    :teacher="$teacher"
                    :teacher-courses="$teacherCourses"
                    :stats="$stats ?? []"
                />

            {{-- 4. Student --}}
            @elseif(auth()->user()->hasRole('student'))
                <x-dashboard.student />

            {{-- 5. Fallback --}}
            @else
                <x-dashboard.basic />
            @endif
               
        </div>
    </div>
</x-app-layout>