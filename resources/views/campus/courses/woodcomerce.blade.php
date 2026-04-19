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
                    
                    <!-- Funcionalidad Avanzada -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-cogs"></i> Funcionalidad Avanzada</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="courseSelect" class="form-label">Seleccionar Cursos:</label>
                                            <select id="courseSelect" class="form-control" multiple size="8">
                                                <option value="">Cargando cursos...</option>
                                            </select>
                                            <small class="form-text text-muted">
                                                Mantén presionado Ctrl para seleccionar múltiples cursos
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Casos de Test Recomendados:</h6>
                                            <div class="list-group">
                                                <a href="#" class="list-group-item list-group-item-action test-case" data-courses="32,59">
                                                    <strong>Aula Oberta Digital</strong><br>
                                                    <small>AOBERTA-001 (parent) + AOBERTA-002 (online)</small>
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action test-case" data-courses="2,48,49">
                                                    <strong>Chi Kung</strong><br>
                                                    <small>CHIKUNG-001 (parent) + CHIKUNG-002/003 (variaciones)</small>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button id="exportSelectedBtn" class="btn btn-warning btn-block">
                                                <i class="fas fa-download"></i> Exportar Cursos Seleccionados
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resultados -->
                    <div id="resultsSection" class="card" style="display: none;">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-bar"></i> Resultados</h5>
                        </div>
                        <div class="card-body">
                            <div id="resultsContent"></div>
                        </div>
                    </div>
                    
                                        
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    
    // Exportación completa
    $('#exportBtn').click(function() {
        window.location.href = '/campus/courses/woodcomerce/export';
    });
    
    // Cargar cursos para select
    loadCourses();
    
    // Exportación de cursos seleccionados
    $('#exportSelectedBtn').click(function() {
        var selectedCourses = $('#courseSelect').val();
        
        if (!selectedCourses || selectedCourses.length === 0) {
            showError('Por favor selecciona al menos un curso');
            return;
        }
        
        exportSelected(selectedCourses);
    });
    
    // Casos de test predefinidos
    $('.test-case').click(function(e) {
        e.preventDefault();
        var courseIds = $(this).data('courses').toString().split(',');
        $('#courseSelect').val(courseIds);
    });
    
    // Cargar lista de cursos
    function loadCourses() {
        $.get('/api/courses/list')
            .done(function(courses) {
                var options = '';
                courses.forEach(function(course) {
                    options += '<option value="' + course.id + '">' + 
                              course.code + ' - ' + course.title + 
                              ' (' + course.format + ')' +
                              '</option>';
                });
                $('#courseSelect').html(options);
            })
            .fail(function() {
                showError('Error al cargar lista de cursos');
            });
    }
    
    // Exportar cursos seleccionados
    function exportSelected(courseIds) {
        showLoading('Exportando cursos seleccionados...');
        
        $.post('/campus/courses/woodcomerce/export-selected', {
                course_ids: courseIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    // Descargar archivo
                    if (response.file_url) {
                        window.location.href = response.file_url;
                    }
                } else {
                    showError('Error: ' + response.error);
                }
            })
            .fail(function() {
                showError('Error de conexión al exportar cursos seleccionados');
            });
    }
    
    // Funciones de UI
    function showLoading(message) {
        $('#resultsContent').html('<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> ' + message + '</div>');
        $('#resultsSection').show();
    }
    
    function showSuccess(message) {
        $('#resultsContent').html('<div class="alert alert-success"><i class="fas fa-check"></i> ' + message + '</div>');
        $('#resultsSection').show();
    }
    
    function showError(message) {
        $('#resultsContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + message + '</div>');
        $('#resultsSection').show();
    }
});
</script>
@endpush
@endsection
