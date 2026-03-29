@extends('campus.shared.layout')

@section('title', __('campus.notification_details'))
@section('subtitle', $notification->title)

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teacher.courses.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                @lang('campus.my_courses')
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teacher.courses.students', $course->id) }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.enrolled_students') }}
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teacher.courses.notifications.index', $course->id) }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.notifications_history') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.notification_details') }}
            </span>
        </div>
    </li>
@endsection

@section('actions')
    <div class="flex space-x-2">
        <a href="{{ route('campus.teacher.courses.notifications.index', $course->id) }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
            <i class="bi bi-arrow-left mr-2"></i>
            {{ __('campus.notifications_history') }}
        </a>
        
        <a href="{{ route('campus.teacher.courses.students', $course->id) }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
            <i class="bi bi-people mr-2"></i>
            {{ __('campus.enrolled_students') }}
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Información de la notificación --}}
    <div class="bg-white shadow-sm rounded-lg border">
        <div class="px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ $notification->title }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('campus.notification_number') }}: {{ $notification->id }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ __('campus.type') }}: {{ $notification->type === 'teacher' ? 'Teacher' : $notification->type }}
                    </span>
                </div>
            </div>
        </div>

        <div class="px-6 py-4">
            <div class="prose max-w-none">
                {!! $notification->content !!}
            </div>
        </div>
    </div>

    {{-- Estadísticas de entrega --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white shadow-sm rounded-lg border p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="bi bi-people-fill text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">{{ __('campus.recipients_count') }}</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ $notification->recipients->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="bi bi-check-circle-fill text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">{{ __('campus.read_count') }}</p>
                    <p class="text-2xl font-semibold text-green-600">
                        {{ $notification->recipients->where('read', true)->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="bi bi-clock-fill text-gray-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">{{ __('campus.created_at') }}</p>
                    <p class="text-lg font-semibold text-gray-600">
                        {{ $notification->published_at ? $notification->published_at->format('d/m/Y H:i') : '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de destinatarios --}}
    <div class="mt-6 bg-white shadow-sm rounded-lg border">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">
                {{ __('campus.sent_to') }} ({{ $notification->recipients->count() }} {{ __('campus.students_total') }})
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.read') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.read_at') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($notification->recipients as $recipient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $recipient->name ?? $recipient->email }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($recipient->read)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ __('campus.yes') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ __('campus.no') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $recipient->read_at ? $recipient->read_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
