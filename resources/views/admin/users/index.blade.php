<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <i class="bi bi-people-fill mr-2"></i>
                {{ __('site.User Management') }}
            </h2>
            <a href="{{ route('admin.users.create') }}">
                <x-primary-button>
                    <i class="bi bi-plus-lg mr-1"></i>{{ __('site.Create User') }} 
                </x-primary-button>
            </a>
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

                    @if (session('warning'))
                        <div class="p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 border border-yellow-300 rounded">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white divide-y divide-gray-200">
                                <tr class="border-b">
                                    <th class="text-left px-4 py-2 w-1/3">{{ __('site.Name') }} </th>
                                    <th class="text-left  px-4 py-2 w-2/20">{{ __('site.Email') }}</th>
                                    <th class="text-left  px-4 py-2 w-2/20">{{ __('site.rol') }}</th>
                                    <th class="text-left  px-4 py-2 w-1/5">{{ __('site.registration_date') }}</th>
                                    <th class="text-left  px-4 py-2">{{ __('site.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                <tr class="border-b">
                                    <td class="text-left px-4 py-2 w-1/3">{!! $user->name !!}</td>
                                    <td class="text-left  px-4 py-2 w-2/20" >{!! $user->email !!}</td>
                                    <td class="text-left  px-4 py-2 w-2/20" >{{ $user->getRoleNames()->first() ??  __('No role') }}</td>
                                    <td class="py-3">{{ $user->created_at->format('d/m/Y') }}</td>
                                    <!-- Celdas de datos... -->
                                    <td class="px-6 py-4  text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="text-indigo-600 hover:text-indigo-900"
                                               title=">{{ __('site.Edit') }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        title=">{{ __('site.Delete') }}"
                                                        onclick="return confirm('{{ addslashes(__('Are you sure to delete this user?')) }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                            @if(optional($user->fcmTokens)->count() > 0)
                                            <div class="card mt-4">
                                                <div class="card-header">
                                                    <h5>Dispositivos Registrados</h5>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-group">
                                                        @foreach($user->fcmTokens as $index => $token)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <small class="text-muted d-block">{{ $token->device_name }}</small>
                                                                <span>{{ substr($token->token, 0, 6) }}...{{ substr($token->token, -6) }}</span>
                                                            </div>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                            @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>