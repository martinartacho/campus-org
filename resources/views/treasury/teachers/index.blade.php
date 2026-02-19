@extends('campus.shared.layout')

@section('title', __('campus.financial_data_management'))
@section('subtitle', __('campus.financial_data_info'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.treasury.teachers.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.teachers') }} / ({{ $season->name ?? 'Sense temporada' }}) ({{ $teachersWithCourses->count() }} prof.)
            </a>
        </div>
    </li>
@endsection

@section('actions')
    <!-- Las acciones de exportación están en el contenido principal -->
@endsection

@section('content')

<!-- Botones de exportación -->
@if($teachersWithCourses->count() > 0)
<div class="mb-4 flex space-x-2">
    <a href="{{ route('campus.treasury.teachers.export', 'xlsx') }}?season={{ $selectedSeasonSlug }}"
        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        <i class="bi bi-file-earmark-excel mr-2"></i>
        Excel
    </a>
    
    <a href="{{ route('campus.treasury.teachers.export', 'csv') }}?season={{ $selectedSeasonSlug }}"
        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
        <i class="bi bi-filetype-csv"></i>
        CSV
    </a>
</div>
@endif

<!-- Selector de temporada -->
@if($seasons->count() > 0)
<div class="mb-6 p-4 bg-white rounded-lg shadow">
    <form method="GET" action="{{ route('campus.treasury.teachers.index') }}" class="flex items-center space-x-4">
        <label for="season" class="font-medium">Filtrar per temporada:</label>
        <select name="season" id="season" 
                class="border rounded px-3 py-2 w-64"
                onchange="this.form.submit()">
            @foreach($seasons as $s)
                <option value="{{ $s->slug }}" 
                        {{ ($selectedSeasonSlug == $s->slug) ? 'selected' : '' }}>
                    {{ $s->name }} ({{ $s->slug }})
                    @if($s->is_current) - ACTUAL @endif
                </option>
            @endforeach
        </select>
        
        @if($selectedSeasonSlug)
            <a href="{{ route('campus.treasury.teachers.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                {{ __('campus.clear_filter') }}
            </a>
        @endif
    </form>
</div>
@endif

@if($teachersWithCourses->count() === 0)
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="bi bi-exclamation-triangle text-yellow-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-700">
                @if(!$season)
                    {{ __('campus.no_active_season') }}
                @else
                    {{ __('campus.no_teachers_in_season', ['season' => $season->name]) }}
                @endif
            </p>
        </div>

    </div>
</div>
@else
<table class="table-auto w-full border">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">{{ __('campus.courses_and_hours') }}</th>
            <th class="p-2 text-left">{{ __('campus.name') }}</th>
            <th class="p-2 text-left">{{ __('campus.email') }}</th>
            <th class="p-2 text-left">{{ __('campus.reminder_status') }}</th>
            <th class="p-2 text-left">{{ __('campus.invoice') }}</th>
            <th class="p-2 text-center">{{ __('campus.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($teachersWithCourses as $teacherData)
            @php
                $user = $teacherData['user'] ?? null;
                $teacherProfile = $teacherData['teacher_profile'] ?? null;
                $courses = $teacherData['courses'] ?? collect();
                $rgpdConsent = $teacherData['rgpd_consent'] ?? null;
            @endphp
            
            @if($user && $teacherProfile && $courses->count() > 0)
                @foreach($courses as $courseData)
                    <tr class="border-t">
                        <td class="p-2 border-l-4 {{ $courseData['has_payment_data'] ? 'border-green-500' : 'border-yellow-500' }}">
                            <div class="text-sm">
                                <div class="font-medium text-gray-800">{{ $courseData['course_title'] }}</div>
                                <div class="text-xs text-gray-600">
                                    {{ $courseData['course_code'] }} • 
                                    <span class="font-medium text-blue-600">{{ $courseData['hours_assigned'] }}h</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-2">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $teacherProfile->nif ?? 'Sense NIF' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-2">{{ $user->email }}</td>
                        <td class="p-2 text-center">
                            @if($courseData['payment_pdf_exists'])
                                <!-- Botón para descargar PDF de pago -->
                                <a href="{{ route('campus.treasury.teachers.payment.pdf', [
                                        'teacher' => $teacherProfile->id,
                                        'season' => $selectedSeason->slug,
                                        'course' => $courseData['course_code']
                                ]) }}"
                                   class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                    <i class="bi bi-file-earmark-pdf mr-1"></i>
                                    Descargar 
                                </a>
                            @elseif($courseData['has_active_payment_token'])
                                <!-- Mostrar fecha de expiración del token -->
                                <div class="text-green-600">
                                    <i class="bi bi-clock-fill"></i>
                                    <div class="text-xs">{{ $courseData['payment_token_expires_at'] }}</div>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="p-2 text-left">
                            {{ $teacherProfile->invoice ?? '-' }}
                        </td>
                        <td class="p-2 text-center">
                            <div class="flex justify-center space-x-2">
                                <form method="POST"
                                        action="{{ route('campus.treasury.teachers.send-access', [
                                                'teacher' => $user->id,
                                                'purpose' => 'payments',
                                                'courseCode' => $courseData['course_code']
                                        ]) }}"
                                        class="inline">
                                    @csrf                                   
                                        <button type="submit"
                                            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                              <i class="bi bi-envelope mr-1"></i>
                                            Recordar  {{  $courseData['course_code']  }} 
                                        </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>
@endif

@endsection