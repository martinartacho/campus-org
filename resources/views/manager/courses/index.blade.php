<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestió de Cursos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold mb-4">
                    Cursos disponibles
                </h3>

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
                            <tr class="border-b">
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
</x-app-layout>
