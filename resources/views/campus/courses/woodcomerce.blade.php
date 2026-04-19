@extends('layouts.app')

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
                    
                    <!-- Botones de Acción -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <button id="previewBtn" class="btn btn-info btn-block">
                                <i class="fas fa-eye"></i> Vista Previa
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button id="exportBtn" class="btn btn-success btn-block">
                                <i class="fas fa-file-csv"></i> Exportar CSV
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button id="testBtn" class="btn btn-warning btn-block">
                                <i class="fas fa-vial"></i> Probar Seleccionados
                            </button>
                        </div>
                    </div>
                    
                    <!-- Testing Section -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-flask"></i> Testing Específico</h5>
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
                        </div>
                    </div>
                    
                    <!-- Resultados -->
                    <div id="resultsSection" class="card mt-4" style="display: none;">
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

<script>
$(document).ready(function() {
    
    // Cargar cursos para testing
    loadCourses();
    
    // Vista previa
    $('#previewBtn').click(function() {
        $.get('/campus/courses/woodcomerce/preview')
            .done(function(response) {
                if (response.success) {
                    showPreview(response.data);
                } else {
                    showError('Error en vista previa: ' + response.error);
                }
            })
            .fail(function() {
                showError('Error de conexión al obtener vista previa');
            });
    });
    
    // Exportación completa
    $('#exportBtn').click(function() {
        window.location.href = '/campus/courses/woodcomerce/export';
    });
    
    // Test con cursos seleccionados
    $('#testBtn').click(function() {
        var selectedCourses = $('#courseSelect').val();
        
        if (!selectedCourses || selectedCourses.length === 0) {
            showError('Por favor selecciona al menos un curso');
            return;
        }
        
        $.post('/campus/courses/woodcomerce/test', {
                course_ids: selectedCourses,
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                if (response.success) {
                    showTestResults(response.data);
                } else {
                    showError('Error en test: ' + response.error);
                }
            })
            .fail(function() {
                showError('Error de conexión en test');
            });
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
    
    // Casos de test predefinidos
    $('.test-case').click(function(e) {
        e.preventDefault();
        var courseIds = $(this).data('courses').toString().split(',');
        $('#courseSelect').val(courseIds);
    });
    
    // Mostrar vista previa
    function showPreview(data) {
        var html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Tipo</th><th>SKU</th><th>Nombre</th><th>Precio</th><th>Stock</th></tr></thead><tbody>';
        
        data.forEach(function(item) {
            html += '<tr>';
            html += '<td><span class="badge badge-' + (item.type === 'variable' ? 'primary' : 'secondary') + '">' + item.type + '</span></td>';
            html += '<td>' + item.sku + '</td>';
            html += '<td>' + item.name + '</td>';
            html += '<td>¥' + item.regular_price + '</td>';
            html += '<td>' + (item.stock_quantity || '-') + '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        html += '<p class="text-muted">Mostrando ' + data.length + ' productos</p>';
        
        $('#resultsContent').html(html);
        $('#resultsSection').show();
    }
    
    // Mostrar resultados del test
    function showTestResults(data) {
        var html = '<div class="alert alert-success">';
        html += '<h6><i class="fas fa-check"></i> Test Completado</h6>';
        html += '<p>Se procesaron ' + data.length + ' productos correctamente</p>';
        html += '</div>';
        
        html += '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Tipo</th><th>SKU</th><th>Nombre</th><th>Precio</th></tr></thead><tbody>';
        
        data.forEach(function(item) {
            html += '<tr>';
            html += '<td><span class="badge badge-' + (item.type === 'variable' ? 'primary' : 'secondary') + '">' + item.type + '</span></td>';
            html += '<td>' + item.sku + '</td>';
            html += '<td>' + item.name + '</td>';
            html += '<td>¥' + item.regular_price + '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        
        $('#resultsContent').html(html);
        $('#resultsSection').show();
    }
    
    // Mostrar error
    function showError(message) {
        $('#resultsContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + message + '</div>');
        $('#resultsSection').show();
    }
});
</script>
@endsection
