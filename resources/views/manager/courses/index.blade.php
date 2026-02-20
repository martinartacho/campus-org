<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestió de Cursos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded shadow">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">
                        Cursos disponibles
                    </h3>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               id="searchManagerCourse" 
                               placeholder="Buscar curso..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <table class="min-w-full text-sm">
                    <thead class="border-b font-semibold text-left">
                        <tr>
                            <th class="py-2">Nom</th>
                            <th>Categoria</th>
                            <th>Temporada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr class="border-b manager-course-row">
                                <td class="py-2">{{ $course->name }}</td>
                                <td>{{ $course->category->name ?? '-' }}</td>
                                <td>{{ $course->season->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-gray-500">
                                    No hi ha cursos
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchManagerCourse');
    const rows = document.querySelectorAll('.manager-course-row');
    
    function updateManagerCourseSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
        });
        
        // Show/hide no results message
        const noResultsMsg = document.getElementById('noManagerCourseResults');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
        
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
        if (searchTerm && visibleRows.length === 0) {
            const msg = document.createElement('tr');
            msg.id = 'noManagerCourseResults';
            msg.innerHTML = `
                <td colspan="3" class="py-4 text-center text-gray-500">
                    No se encontraron cursos
                </td>
            `;
            searchInput.closest('.bg-white').querySelector('tbody').appendChild(msg);
        }
    }
    
    searchInput.addEventListener('input', updateManagerCourseSearch);
});
</script>
</x-app-layout>
