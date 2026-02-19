{{-- resources/views/campus/categories/index.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('campus.categories'))
@section('subtitle', __('campus.categories'))

@section('breadcrumbs')
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ __('campus.categories') }}</span>
        </div>
    </li>
@endsection

@section('actions')
    
    <x-campus-button href="{{ route('campus.categories.create') }}" variant="header">
        <i class="bi bi-plus-lg me-2"></i>
        {{ __('campus.new_category') }} 
    </x-campus-button>
    
   
@endsection

@section('content')
    <div class="overflow-x-auto">
        @if($categories->isEmpty())
            <div class="text-center py-12">
                <i class="bi bi-tags text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('campus.no_records') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('campus.no_records') }}</p>
                <x-primary-button href="{{ route('campus.categories.create') }}">
                    <i class="bi bi-plus-lg me-2"></i>
                    {{ __('campus.create_category') }} popo
                </x-primary-button>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.category_parent') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.courses') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.status') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 {{ $category->color_class ?? 'bg-blue-100' }} rounded-lg flex items-center justify-center">
                                    <i class="bi bi-{{ $category->icon ?? 'tag' }} text-{{ $category->color ?? 'blue' }}-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $category->name }}
                                        @if($category->is_featured)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="bi bi-star-fill me-1"></i> {{ __('campus.category_is_featured') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($category->description)
                                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ $category->description }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($category->parent)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="bi bi-diagram-2 me-1"></i>
                                        {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">{{ __('campus.no_parent_category') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                <i class="bi bi-book me-1"></i>
                                {{ $category->courses_count ?? 0 }} {{ __('campus.courses') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="bi bi-check-circle me-1"></i> {{ __('campus.active') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="bi bi-x-circle me-1"></i> {{ __('campus.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('campus.categories.edit', $category) }}" 
                                   class="text-blue-600 hover:text-blue-900"
                                   title="{{ __('campus.edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('campus.categories.show', $category) }}" 
                                   class="text-green-600 hover:text-green-900"
                                   title="{{ __('campus.view') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('campus.categories.destroy', $category) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('{{ __('campus.category_delete_confirmation') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900"
                                            title="{{ __('campus.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{-- Paginación --}}
            <div class="mt-6">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection