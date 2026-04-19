@extends('campus.shared.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-download"></i>
                        Exportación WooCommerce v2
                    </h3>
                </div>
                <div class="card-body">
                    
                    <!-- Información del Sistema -->
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Información del Sistema</h5>
                        <p class="mb-0">
                            Sistema de exportación ETL para WooCommerce. Procesa cursos con lógica de productos variables y variaciones.
                        </p>
                    </div>
                    
                    <!-- Botón de Acción -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <button id="exportBtn" class="btn btn-success btn-block btn-lg">
                                <i class="fas fa-file-csv"></i> Exportar CSV Completo
                            </button>
                        </div>
                        <div class="col-md-8">
                            <div class="alert alert-success">
                                <i class="fas fa-info-circle"></i>
                                <strong>Funcionalidad simplificada:</strong> Solo exportación directa de CSV con todos los cursos.
                            </div>
                        </div>
                    </div>
                    
                                        
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // Exportación completa
    $('#exportBtn').click(function() {
        window.location.href = '/campus/courses/woodcomerce/export';
    });
    
});
</script>
@endsection
