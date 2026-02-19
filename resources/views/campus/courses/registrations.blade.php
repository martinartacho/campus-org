@extends('campus.shared.layout')

@section('title', __('campus.registrations'))
@section('subtitle', $course->title)

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.courses.index') }}"
               class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.courses') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.registrations') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-5xl bg-white shadow rounded-lg p-6">

    <h2 class="text-lg font-semibold mb-4">
        {{ __('campus.enrolled_students') }}
    </h2>

    @if($registrations->isEmpty())
        <p class="text-gray-500">
            {{ __('campus.no_students_enrolled') }}
        </p>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-600">
                    <th class="py-2">{{ __('campus.student') }}</th>
                    <th class="py-2">{{ __('campus.email') }}</th>
                    <th class="py-2">{{ __('campus.status') }}</th>
                    <th class="py-2">{{ __('campus.registered_at') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registrations as $registration)
                    <tr class="border-b last:border-0">
                        <td class="py-2">
                            {{ $registration->student->last_name }},
                            {{ $registration->student->first_name }}
                        </td>
                        <td class="py-2 text-gray-500">
                            {{ $registration->student->email ?? '—' }}
                        </td>
                        <td class="py-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                                @if($registration->status === 'confirmed')
                                    bg-green-100 text-green-800
                                @elseif($registration->status === 'pending')
                                    bg-yellow-100 text-yellow-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ __('campus.registration_status_' . $registration->status) }}
                            </span>
                        </td>
                        <td class="py-2 text-gray-500">
                            {{ $registration->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
