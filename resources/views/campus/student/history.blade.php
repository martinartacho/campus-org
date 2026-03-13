{{-- resources/views/campus/student/history.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('Historial Acadèmic'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('Historial Acadèmic') }}</h1>
        <p class="text-gray-600">{{ __('Cursos completats') }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($registrations->isEmpty())
            <div class="p-8 text-center">
                <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-clock-history text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">{{ __('Encara no has completat cap curs') }}</h3>
                <p class="text-gray-500">
                    {{ __('Quan completis cursos, apareixeran aquí.') }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Curs') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Temporada') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Data finalització') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Estat') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($registrations as $registration)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $registration->course->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $registration->course->code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $registration->course->season->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $registration->updated_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle mr-1"></i>
                                        {{ __('Completat') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection