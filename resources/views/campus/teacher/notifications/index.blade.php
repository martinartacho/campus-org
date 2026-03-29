@extends('campus.shared.layout')

@section('title', __('campus.notifications_history'))
@section('subtitle', $course->title)

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
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.notifications_history') }}
            </span>
        </div>
    </li>
@endsection

@section('actions')
    <div class="flex space-x-2">
        <a href="{{ route('campus.teacher.courses.students', $course->id) }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
            <i class="bi bi-arrow-left mr-2"></i>
            {{ __('campus.enrolled_students') }}
        </a>
        
        <a href="{{ route('campus.teacher.courses.notifications.create', $course->id) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
            <i class="bi bi-bell-fill mr-2"></i>
            {{ __('campus.create_notification') }}
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    @if($notifications->count() === 0)
        {{-- Sin notificaciones --}}
        <div class="text-center py-12 bg-white rounded-lg shadow-sm border">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.279V5a2 2 0 10-4 0v2.279a6.002 6.002 0 00-4 5.279v3.159c0 .538-.214 1.055-.595 1.405L15 17zm-3 0v-2a3 3 0 116 0v2a3 3 0 11-6 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">
                {{ __('campus.no_notifications') }}
            </h3>
            <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                {{ __('campus.no_notifications_desc') }}
            </p>
        </div>
    @else
        {{-- Lista de notificaciones --}}
        <div class="bg-white shadow-sm rounded-lg border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.notification_number') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.notification_title') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.sent_to') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.recipients_count') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.read_count') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.created_at') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($notifications as $notification)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $notification->id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $notification->title }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ Str::limit(strip_tags($notification->content), 100) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($notification->recipient_type === 'all')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ __('campus.all_course_students') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $notification->recipients->count() }} {{ __('campus.selected_students_count') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $notification->recipients->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $notification->recipients->where('read', true)->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $notification->recipients->where('read', true)->count() }} / {{ $notification->recipients->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $notification->published_at ? $notification->published_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('campus.teacher.courses.notifications.show', [$course->id, $notification->id]) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        {{ __('campus.notification_details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
