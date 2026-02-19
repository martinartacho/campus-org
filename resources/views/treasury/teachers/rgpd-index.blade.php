@extends('campus.shared.layout')

@section('title', __('campus.rgpd_consent_management'))
@section('subtitle', __('campus.rgpd_consent_management'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.treasury.teachers.rgpd.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.rgpd_consent_management') }}
            </a>
        </div>
    </li>
@endsection

@section('actions')
    <!-- Las acciones específicas de RGPD están en el contenido principal -->
@endsection

@section('content')

<!-- Acciones específicas para RGPD -->
@if($teachersWithCourses->count() > 0)
<div class="mb-4 flex space-x-2">
    <a href="{{ route('campus.treasury.teachers.export', 'xlsx') }}?season={{ $selectedSeasonSlug }}&type=rgpd"
        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        <i class="bi bi-file-earmark-excel mr-2"></i>
        Excel RGPD
    </a>
    
    <a href="{{ route('campus.treasury.teachers.export', 'csv') }}?season={{ $selectedSeasonSlug }}&type=rgpd"
        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
        <i class="bi bi-filetype-csv"></i>
        CSV RGPD
    </a>
</div>
@endif

<!-- Selector de temporada -->
@if($seasons->count() > 0)
<div class="mb-6 p-4 bg-white rounded-lg shadow">
    <form method="GET" action="{{ route('campus.treasury.teachers.rgpd.index') }}" class="flex items-center space-x-4">
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
            <option value="">Totes les temporades</option>
        </select>
    </form>
</div>
@endif

@if($teachersWithCourses->count() > 0)
<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="table-auto w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">{{ __('campus.name') }}</th>
                <th class="p-3 text-left">{{ __('campus.email') }} Usuari/Professor</th>
                <th class="p-3 text-center">{{ __('campus.rgpd_status') }}</th>
                <th class="p-3 text-center">{{ __('campus.actions') }}</th>
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
                
                @if($user && $teacherProfile)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $teacherProfile->dni ?? 'Sense NIF' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3">{{ $user->email }} / {{ $teacherProfile->email }}</td>

                        <td class="p-3 text-center">
                            @if($teacherData['has_rgpd_consent'])
                                <div class="flex flex-col items-center">
                                    <span class="text-green-600 font-medium">
                                        ✅ Acceptat
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $rgpdConsent->accepted_at->format('d/m/Y H:i') }}
                                    </span>
                                    @if($rgpdConsent->delegated_by_user_id)
                                        <span class="text-xs text-blue-600">(Delegat)</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-red-600 font-medium">
                                    ❌ Pendent
                                </span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <div class="flex justify-center space-x-2">
                                <!-- Ver historial -->
                                <a href="{{ route('campus.treasury.teachers.consents', $user->id) }}"
                                   class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                    <i class="bi bi-clock-history mr-1"></i>
                                    Historial
                                </a>
                                
                                <!-- Descargar PDF -->
                                @if($rgpdConsent)
                                    <a href="{{ route('consents.download', $rgpdConsent) }}"
                                       class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
                                        <i class="bi bi-download mr-1"></i>
                                        PDF
                                    </a>
                                @endif
                                
                                <!-- Enviar recordatorio -->
                                @if(!$teacherData['has_rgpd_consent'])
                                    <form method="POST"
                                          action="{{ route('campus.treasury.teachers.send-access', [
                                                  'teacher' => $user->id,
                                                  'purpose' => 'consent'
                                          ]) }}"
                                          class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                            <i class="bi bi-envelope mr-1"></i>
                                            Recordar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

@else
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
    <i class="bi bi-info-circle text-yellow-600 text-3xl mb-3"></i>
    <h3 class="text-lg font-medium text-yellow-800 mb-2">
        @if(!$season)
            {{ __('campus.no_active_season') }}
        @else
            {{ __('campus.no_teachers_in_season', ['season' => $season->name]) }}
        @endif
    </h3>
    <p class="text-yellow-700">
        No hi ha professors per mostrar en aquesta temporada.
    </p>
</div>
@endif

@endsection
