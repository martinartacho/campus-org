@extends('campus.shared.layout')

@section('title', __('site.Backup_Management'))
@section('subtitle', __('Administración de backups automáticos'))

@section('actions')
   
@endsection

@section('content')


    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">{{ __('site.Last_24h') }}</h5>
                    <h2 class="mb-0">{{ $stats['last_24h'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">{{ __('site.Last_7_days') }}</h5>
                    <h2 class="mb-0">{{ $stats['last_7days'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">{{ __('site.Success_Rate') }}</h5>
                    <h2 class="mb-0">{{ $stats['success_rate'] }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">{{ __('site.Last_Backup') }}</h5>
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
                    <h5 class="mb-0">� {{ __('site.Backup_Records') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Total:</strong> {{ $diskUsage['total'] }}
                        </div>
                        <div class="col-md-3">
                            <strong>Usat:</strong> {{ $diskUsage['used'] }}
                        </div>
                        <div class="col-md-3">
                            <strong>Lliure:</strong> {{ $diskUsage['free'] }}
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
                        📋 {{ __('site.Backup_Records') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="altres-tab" data-bs-toggle="tab" 
                            data-bs-target="#altres" type="button" role="tab">
                         <i class="bi bi-info-circle"></i>  Altres
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
                                                <th>{{ __('site.Date') }}</th>
                                                <th>{{ __('site.Environment') }}</th>
                                                <th>{{ __('site.File') }}</th>
                                                <th>{{ __('site.Size') }}</th>
                                                <th>{{ __('site.Status') }}</th>
                                              <!--   <th>Acciones</th> -->
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
                                                   <!--  <td>
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
                                                    </td> -->
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> {{ __('site.No_backup_records') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab de altres -->
               <div class="tab-pane fade" id="altres" role="tabpanel">
                    <div class="card">
                        <div class="alert alert-warning mb-4">
                        <h6><i class="bi bi-shield-exclamation"></i> {{ __('site.Security_Instructions') }}</h6>
                                <p class="mb-2">{{ __('site.Security_Instructions_Desc') }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>{{ __('Step_1') }}:</strong>
                                        <code>cd /var/www/dev.upg.cat</code>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>{{ __('Step_2') }}:</strong>
                                        <code>php artisan backup:database --environment=dev</code>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <strong>{{ __('Step_3') }}:</strong>
                                        <code>php artisan backup:database --environment=prod</code>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>{{ __('Step_4') }}:</strong>
                                        <code>ls -la /var/www/backups/</code>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0"><small class="text-muted">{{ __('site.Security_Note') }}</small></p>
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
<!-- Solo información - sin ejecución desde web -->
@endpush
