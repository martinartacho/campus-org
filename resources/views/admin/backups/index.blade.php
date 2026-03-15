@extends('layouts.app')

@section('title', 'Gestión de Backups')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">🔄 Gestión de Backups</h1>
                <div>
                    <button type="button" class="btn btn-primary" onclick="executeBackup('dev')">
                        <i class="bi bi-download"></i> Backup Dev
                    </button>
                    <button type="button" class="btn btn-warning" onclick="executeBackup('prod')">
                        <i class="bi bi-download"></i> Backup Prod
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Últimas 24h</h5>
                    <h2 class="mb-0">{{ $stats['last_24h'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Últimos 7 días</h5>
                    <h2 class="mb-0">{{ $stats['last_7days'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Tasa de Éxito</h5>
                    <h2 class="mb-0">{{ $stats['success_rate'] }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Último Backup</h5>
                    <h6 class="mb-0">{{ $stats['last_backup'] }}</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Uso de Disco -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">💾 Uso de Disco</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Total:</strong> {{ $diskUsage['total'] }}
                        </div>
                        <div class="col-md-3">
                            <strong>Usado:</strong> {{ $diskUsage['used'] }}
                        </div>
                        <div class="col-md-3">
                            <strong>Libre:</strong> {{ $diskUsage['free'] }}
                        </div>
                        <div class="col-md-3">
                            <strong>Backups:</strong> {{ $diskUsage['backup_used'] }}
                        </div>
                    </div>
                    <div class="progress mt-3">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $diskUsage['usage_percentage'] }}%"
                             aria-valuenow="{{ $diskUsage['usage_percentage'] }}" 
                             aria-valuemin="0" aria-valuemax="100">
                            {{ $diskUsage['usage_percentage'] }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs" id="backupTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="records-tab" data-bs-toggle="tab" 
                            data-bs-target="#records" type="button" role="tab">
                        📋 Registros
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="files-tab" data-bs-toggle="tab" 
                            data-bs-target="#files" type="button" role="tab">
                        📁 Archivos
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="backupTabsContent">
                <!-- Tab de Registros -->
                <div class="tab-pane fade show active" id="records" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            @if($backups->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Entorno</th>
                                                <th>Archivo</th>
                                                <th>Tamaño</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($backups as $backup)
                                                <tr>
                                                    <td>{{ $backup->created_at_formatted }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $backup->environment === 'prod' ? 'warning' : 'info' }}">
                                                            {{ strtoupper($backup->environment) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $backup->filename }}</td>
                                                    <td>{{ $backup->file_size_formatted }}</td>
                                                    <td>{!! $backup->status_badge !!}</td>
                                                    <td>
                                                        @if($backup->status === 'success' && $backup->filename)
                                                            <a href="{{ route('admin.backups.download', $backup->filename) }}" 
                                                               class="btn btn-sm btn-outline-primary" title="Descargar">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteBackup('{{ $backup->filename }}')" title="Eliminar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No hay registros de backups disponibles.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab de Archivos -->
                <div class="tab-pane fade" id="files" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            @if($recentBackups->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Archivo</th>
                                                <th>Tamaño</th>
                                                <th>Modificado</th>
                                                <th>En BD</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentBackups as $backup)
                                                <tr>
                                                    <td>{{ $backup['filename'] }}</td>
                                                    <td>{{ $backup['size'] }}</td>
                                                    <td>{{ $backup['modified'] }}</td>
                                                    <td>
                                                        @if($backup['exists_in_db'])
                                                            <span class="badge bg-success">✅</span>
                                                        @else
                                                            <span class="badge bg-warning">⚠️</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.backups.download', $backup['filename']) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="Descargar">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteBackup('{{ $backup['filename'] }}')" title="Eliminar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No hay archivos de backup disponibles.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">¿Está seguro de realizar esta acción?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmButton">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de progreso -->
<div class="modal fade" id="progressModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Ejecutando backup...</span>
                </div>
                <h5 class="mt-3">Ejecutando backup...</h5>
                <p id="progressMessage" class="text-muted">Por favor espere.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function executeBackup(environment) {
    $('#progressModal').modal('show');
    $('#progressMessage').text('Ejecutando backup de ' + environment + '...');
    
    fetch('{{ route("admin.backups.execute") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            environment: environment
        })
    })
    .then(response => response.json())
    .then(data => {
        $('#progressModal').modal('hide');
        
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        $('#progressModal').modal('hide');
        alert('❌ Error en la solicitud: ' + error);
    });
}

function deleteBackup(filename) {
    $('#confirmMessage').text('¿Está seguro de eliminar el backup "' + filename + '"?');
    $('#confirmButton').off('click').on('click', function() {
        $('#confirmModal').modal('hide');
        
        fetch('{{ route("admin.backups.destroy", ":filename") }}'.replace(':filename', filename), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('❌ Error eliminando backup: ' + error);
        });
    });
    
    $('#confirmModal').modal('show');
}
</script>
@endpush
