@extends('campus.shared.layout')

@section('title', 'Importar Ordres WordPress')
@section('subtitle', 'Gestió d\'importació d\'ordres des de WordPress')

@section('actions')
    <div class="flex space-x-2">
        <a href="{{ route('campus.ordres.validate') }}" class="btn btn-secondary">
            <i class="bi bi-check-circle mr-2"></i>Validar Ordres
        </a>
    </div>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Missatge d'èxit o error -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="bi bi-check-circle-fill text-green-600 mr-3"></i>
                <div>
                    <h3 class="text-green-800 font-semibold">Importació amb èxit!</h3>
                    <p class="text-green-700 mt-1">
                        {{ session('success') }}
                        @if($stats['pending'] > 0)
                            <br><strong>Següent pas:</strong> 
                            <a href="{{ route('campus.ordres.validate') }}" class="text-blue-600 hover:text-blue-800 underline">
                                <i class="bi bi-arrow-right-circle mr-1"></i>Validar les {{ $stats['pending'] }} ordres pendents
                            </a>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="bi bi-exclamation-triangle-fill text-red-600 mr-3"></i>
                <div>
                    <h3 class="text-red-800 font-semibold">Error en la importació</h3>
                    <p class="text-red-700 mt-1">
                        {{ session('error') }}
                        <br><strong>Recomanació:</strong> 
                        <a href="#" onclick="showLogs()" class="text-blue-600 hover:text-blue-800 underline">
                            <i class="bi bi-file-text mr-1"></i>Consulta els logs per veure els detalls
                        </a>
                    </p>
                </div>
            </div>
        </div>
    @endif
    <!-- Estadístiques -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Ordres</div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_ordres'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Pendents</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Matched</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['matched'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Revisió Manual</div>
            <div class="text-2xl font-bold text-orange-600">{{ $stats['manual'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Errors</div>
            <div class="text-2xl font-bold text-red-600">{{ $stats['error'] }}</div>
        </div>
    </div>

    <!-- Formulari d'importació -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Importar Ordres des de CSV</h3>
        
        <form action="{{ route('campus.ordres.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div>
                <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Fitxer CSV d'Ordres
                </label>
                <input type="file" 
                       id="csv_file" 
                       name="csv_file" 
                       accept=".csv,.txt"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-1 text-sm text-gray-500">
                    Format CSV: "First Name (Billing)","Last Name (Billing)","Email (Billing)","Phone (Billing)","Item Name","codi","Quantity"
                </p>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload mr-2"></i>Importar Ordres
                </button>
                <a href="{{ route('campus.ordres.import') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise mr-2"></i>Refrescar
                </a>
            </div>
        </form>
    </div>

    <!-- Últimes Importacions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Últimes Importacions</h3>
        
        @if($recentImports->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codi WP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Importat</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentImports as $ordre)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ordre->wp_first_name }} {{ $ordre->wp_last_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ordre->wp_email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ordre->wp_item_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ordre->wp_code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($ordre->validation_status === 'matched') bg-green-100 text-green-800
                                        @elseif($ordre->validation_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($ordre->validation_status === 'manual') bg-orange-100 text-orange-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $ordre->formatted_validation_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ordre->imported_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="bi bi-inbox text-4xl mb-2"></i>
                <p>Cap importació recent</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn {
        @apply px-4 py-2 rounded-md font-medium text-sm transition-colors duration-200;
    }
    .btn-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2;
    }
    .btn-secondary {
        @apply bg-gray-600 text-white hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2;
    }
</style>
@endpush
