{{-- resources/views/components/campus-parent-category-select.blade.php --}}
@props(['categories', 'selected' => null, 'exclude' => null])

<select {{ $attributes->merge(['class' => 'border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500']) }}>
    <option value="">{{ __('campus.category_select_parent') }}</option>
    @foreach($categories as $category)
        @if($exclude && $category->id == $exclude)
            @continue
        @endif
        <option value="{{ $category->id }}" {{ $selected == $category->id ? 'selected' : '' }}>
            {{ $category->name }}
            @if($category->parent)
                ({{ $category->parent->name }})
            @endif
        </option>
    @endforeach
</select>