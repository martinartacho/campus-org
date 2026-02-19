<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <i class="bi bi-chat-dots-fill mr-2"></i>
                {{ __('site.Feedback de usuarios') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($feedbacks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-white divide-y divide-gray-200">
                                    <tr class="border-b">
                                        <th class="text-left px-4 py-2">ID</th>
                                        <th class="text-left px-4 py-2">Email</th>
                                        <th class="text-left px-4 py-2">Tipo</th>
                                        <th class="text-left px-4 py-2">Mensaje</th>
                                        <th class="text-left px-4 py-2">Fecha</th>
                                        <th class="text-left px-4 py-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($feedbacks as $feedback)
                                        <tr class="border-b">
                                            <td class="px-4 py-2">{{ $feedback->id }}</td>
                                            <td class="px-4 py-2">{{ $feedback->email ?? '—' }}</td>
                                            <td class="px-4 py-2">{{ $feedback->type ?? '—' }}</td>
                                            <td class="px-4 py-2">{{ $feedback->message }}</td>
                                            <td class="px-4 py-2">{{ $feedback->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-2">
                                                <form action="{{ route('admin.feedback.destroy', $feedback->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este mensaje de feedback?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:text-red-900" title="Eliminar feedback">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $feedbacks->links() }}
                        </div>
                    @else
                        <p class="text-gray-600">No se ha recibido ningún feedback todavía.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
