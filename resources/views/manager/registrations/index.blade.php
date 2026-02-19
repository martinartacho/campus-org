<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inscripcions
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold mb-4">
                    Llistat d’inscripcions
                </h3>

                <table class="min-w-full text-sm">
                    <thead class="border-b text-left font-semibold">
                        <tr>
                            <th class="py-2">Alumne</th>
                            <th class="py-2">Contacte</th>
                            <th>Curs</th>
                            <th>Temporada</th>
                            <th>Data / estat </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                            <tr class="border-b">
                                <td class="py-2">
                                    {{ $registration->student->first_name ?? '-' }}  {{ $registration->student->last_name ?? '-' }} 
                                </td>
                                <td class="py-2">
                                     {{ $registration->student->email ?? '-' }} {{ $registration->student->phone ?? '-' }}
                                </td>
                                <td>
                                    {{ $registration->course->title ?? '-' }}
                                </td>
                                <td>
                                    {{ $registration->course->season->name ?? '-' }}
                                </td>
                                <td>
                                    {{ $registration->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    {{ $registration->getFormattedStatusAttribute() }}
                                </td>
                                <td class="px-3 py-2 text-right">
    <button
        @click="open = true"
        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
    >
        Detalls
    </button>

    {{-- Modal --}}
    <div
        x-data="{ open: false }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    >
        <div
            @click.outside="open = false"
            class="bg-white rounded-lg shadow-xl w-full max-w-md p-6"
        >
            <h3 class="text-lg font-semibold mb-4">
                📋 Dades de la inscripció
            </h3>

            <div class="space-y-2 text-sm">
                <div>
                    <span class="text-gray-500">Email:</span><br>
                    <span class="font-medium">
                        {{ $registration->student->email ?? '-' }}
                    </span>
                </div>

                <div>
                    <span class="text-gray-500">Telèfon:</span><br>
                    <span class="font-medium">
                        {{ $registration->student->phone ?? '-' }}
                    </span>
                </div>

                <div>
                    <span class="text-gray-500">Estat:</span><br>
                    <span class="font-medium">
                        {{ $registration->getFormattedStatusAttribute() }}
                    </span>
                </div>
            </div>

            <div class="mt-6 text-right">
                <button
                    @click="open = false"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm"
                >
                    Tancar
                </button>
            </div>
        </div>
    </div>
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-gray-500">
                                    No hi ha inscripcions
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
