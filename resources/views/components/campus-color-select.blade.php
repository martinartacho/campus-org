{{-- resources/views/components/campus-color-select.blade.php --}}
@props(['selected' => 'blue'])

<select {{ $attributes->merge(['class' => 'border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500']) }}>
    <option value="blue" {{ $selected == 'blue' ? 'selected' : '' }}>{{ __('campus.color_blue') }}</option>
    <option value="green" {{ $selected == 'green' ? 'selected' : '' }}>{{ __('campus.color_green') }}</option>
    <option value="red" {{ $selected == 'red' ? 'selected' : '' }}>{{ __('campus.color_red') }}</option>
    <option value="yellow" {{ $selected == 'yellow' ? 'selected' : '' }}>{{ __('campus.color_yellow') }}</option>
    <option value="purple" {{ $selected == 'purple' ? 'selected' : '' }}>{{ __('campus.color_purple') }}</option>
    <option value="pink" {{ $selected == 'pink' ? 'selected' : '' }}>{{ __('campus.color_pink') }}</option>
    <option value="indigo" {{ $selected == 'indigo' ? 'selected' : '' }}>{{ __('campus.color_indigo') }}</option>
    <option value="gray" {{ $selected == 'gray' ? 'selected' : '' }}>{{ __('campus.color_gray') }}</option>
    <option value="orange" {{ $selected == 'orange' ? 'selected' : '' }}>{{ __('campus.color_orange') }}</option>
    <option value="teal" {{ $selected == 'teal' ? 'selected' : '' }}>{{ __('campus.color_teal') }}</option>
</select>