{{-- resources/views/components/campus-icon-select.blade.php --}}
@props(['selected' => 'tag'])

<select {{ $attributes->merge(['class' => 'border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500']) }}>
    <option value="tag" {{ $selected == 'tag' ? 'selected' : '' }}>🏷️ Tag</option>
    <option value="book" {{ $selected == 'book' ? 'selected' : '' }}>📚 Llibre</option>
    <option value="graduation-cap" {{ $selected == 'graduation-cap' ? 'selected' : '' }}>🎓 Barret de graduació</option>
    <option value="laptop" {{ $selected == 'laptop' ? 'selected' : '' }}>💻 Ordinador</option>
    <option value="code" {{ $selected == 'code' ? 'selected' : '' }}>💻 Codi</option>
    <option value="palette" {{ $selected == 'palette' ? 'selected' : '' }}>🎨 Paleta</option>
    <option value="music-note" {{ $selected == 'music-note' ? 'selected' : '' }}>🎵 Nota musical</option>
    <option value="dumbbell" {{ $selected == 'dumbbell' ? 'selected' : '' }}>🏋️ Pes</option>
    <option value="globe" {{ $selected == 'globe' ? 'selected' : '' }}>🌎 Globus</option>
    <option value="calculator" {{ $selected == 'calculator' ? 'selected' : '' }}>🧮 Calculadora</option>
    <option value="flask" {{ $selected == 'flask' ? 'selected' : '' }}>🧪 Flascó</option>
    <option value="briefcase" {{ $selected == 'briefcase' ? 'selected' : '' }}>💼 Maletí</option>
</select>