{{-- resources/views/campus/categories/show.blade.php --}}
@extends('campus.shared.layout')

@section('title', $category->name)
@section('subtitle', __('campus.category_details'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.categories.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.categories') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ __('campus.category_details') }}</span>
        </div>
    </li>
@endsection

@section('actions')
    <div class="flex space-x-2">
        <x-campus-primary-button href="{{ route('campus.categories.edit', $category) }}">
            <i class="bi bi-pencil me-2"></i>
            {{ __('campus.edit') }}
        </x-campus-primary-button>
        
        @if($category->courses_count == 0)
            <form action="{{ route('campus.categories.destroy', $category) }}" method="POST" 
                  onsubmit="return confirm('{{ __('campus.category_delete_confirmation') }}')">
                @csrf
                @method('DELETE')
                <x-campus-danger-button type="submit">
                    <i class="bi bi-trash me-2"></i>
                    {{ __('campus.delete') }}
                </x-campus-danger-button>
            </form>
        @else
            <button type="button" 
                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-600 uppercase tracking-widest cursor-not-allowed"
                    title="{{ __('campus.category_has_courses_warning') }}">
                <i class="bi bi-trash me-2"></i>
                {{ __('campus.delete') }}
            </button>
        @endif
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Información general --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Datos principales --}}
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-lg border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-3">
                                <div class="h-16 w-16 {{ $category->color ? 'bg-' . $category->color . '-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center">
                                    <i class="bi bi-{{ $category->icon ?? 'tag' }} {{ $category->color ? 'text-' . $category->color . '-600' : 'text-blue-600' }} text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $category->name }}</h3>
                                    @if($category->description)
                                        <p class="text-gray-600 mt-2">{{ $category->description }}</p>
                                    @endif
                                </div>
                                
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mt-6">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="bi bi-{{ $category->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                    {{ $category->is_active ? __('campus.active') : __('campus.inactive') }}
                                </span>
                                
                                @if($category->is_featured)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                                        <i class="bi bi-star-fill me-1"></i>
                                        {{ __('campus.category_is_featured') }}
                                    </span>
                                @endif
                                
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">
                                    <i class="bi bi-{{ $category->icon ?? 'tag' }} me-1"></i>
                                    {{ __('campus.category_icon') }}
                                </span>
                                
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $category->color ? 'bg-' . $category->color . '-100 text-' . $category->color . '-800' : 'bg-blue-100 text-blue-800' }}">
                                    <i class="bi bi-palette me-1"></i>
                                    {{ $category->color ? __('campus.color_' . $category->color) : __('campus.color_blue') }}
                                </span>
                            </div>
                        </div>
                        
                        
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($category->parent)
                            <div class="p-4 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg me-3">
                                        <i class="bi bi-diagram-2 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-blue-800">{{ __('campus.category_parent') }}</div>
                                        <div class="font-semibold text-gray-900">{{ $category->parent->name }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 rounded-lg me-3">
                                    <i class="bi bi-sort-numeric-up text-green-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-green-800">{{ __('campus.category_order') }}</div>
                                    <div class="font-semibold text-gray-900">{{ $category->order }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Estadísticas --}}
            <div>
                <div class="bg-white p-6 rounded-lg border border-gray-200">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="bi bi-graph-up me-2"></i>
                        {{ __('campus.statistics') }}
                    </h4>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('campus.courses_in_category') }}</span>
                            <span class="font-semibold">{{ $category->courses_count ?? 0 }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('campus.subcategories') }}</span>
                            <span class="font-semibold">{{ $category->children_count ?? 0 }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('campus.total_courses') }}</span>
                            <span class="font-semibold">{{ $stats['total_courses'] ?? 0 }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('campus.active_courses') }}</span>
                            <span class="font-semibold">{{ $stats['active_courses'] ?? 0 }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="text-sm text-gray-500">{{ __('campus.created_at') }}</div>
                        <div class="font-medium">{{ $category->created_at?->format('d/m/Y H:i') ?? 'No disponible' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Subcategorías --}}
        @if($category->children->isNotEmpty())
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-6">
                    <i class="bi bi-diagram-2 me-2"></i>
                    {{ __('campus.subcategories') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($category->children as $child)
                        <a href="{{ route('campus.categories.show', $child) }}" 
                           class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 {{ $child->color ? 'bg-' . $child->color . '-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center">
                                    <i class="bi bi-{{ $child->icon ?? 'tag' }} {{ $child->color ? 'text-' . $child->color . '-600' : 'text-blue-600' }}"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $child->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $child->courses_count ?? 0 }} {{ __('campus.courses') }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
        
        {{-- Cursos de esta categoría --}}
        <div class="bg-white p-6 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="bi bi-book me-2"></i>
                    {{ __('campus.courses_in_category') }}
                </h3>
                
                <x-campus-primary-button href="{{ route('campus.courses.create') }}?category={{ $category->id }}" size="sm">
                    <i class="bi bi-plus-lg me-1"></i>
                    {{ __('campus.new_course') }}
                </x-campus-primary-button>
            </div>
            
            @if($category->courses->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <i class="bi bi-book text-3xl mb-3"></i>
                    <p>{{ __('campus.no_courses') }}</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.courses') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.seasons') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.teachers') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.registrations') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.status') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($category->courses as $course)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <a href="{{ route('campus.courses.show', $course) }}" 
                                       class="font-medium text-blue-600 hover:text-blue-800">
                                        {{ $course->title }}
                                    </a>
                                </td>
                                <td class="px-4 py-4">
                                    @if($course->season)
                                        <span class="text-sm bg-gray-100 text-gray-800 px-2 py-1 rounded">
                                            {{ $course->season->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">{{ __('campus.no_season') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if($course->teachers->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($course->teachers as $teacher)
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                    {{ $teacher->user->name ?? $teacher->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">{{ __('campus.no_teachers') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-medium">{{ $course->registrations_count ?? 0 }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    @if($course->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ __('campus.active') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ __('campus.inactive') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        
        {{-- Acciones adicionales --}}
        <div class="flex justify-end space-x-4">
            <x-campus-secondary-button href="{{ route('campus.categories.index') }}">
                <i class="bi bi-x-lg me-2"></i>
                {{ __('campus.cancel') }}
            </x-campus-secondary-button>
            
            @if($category->is_active)
                <form action="{{ route('campus.categories.toggleActive', $category) }}" method="POST">
                    @csrf
                    <x-campus-button type="submit" color="red">
                        <i class="bi bi-x-circle me-2"></i>
                        {{ __('campus.deactivate') }}
                    </x-campus-button>
                </form>
            @else
                <form action="{{ route('campus.categories.toggleActive', $category) }}" method="POST">
                    @csrf
                    <x-campus-button type="submit" color="green">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ __('campus.activate') }}
                    </x-campus-button>
                </form>
            @endif
        </div>
    </div>
@endsection