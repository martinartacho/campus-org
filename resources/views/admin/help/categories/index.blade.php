@extends('campus.shared.layout')

@section('title', 'Categories d\'Ajuda')
@section('subtitle', 'Gestió de categories d\'ajuda del campus')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Categories d\'Ajuda</h1>
                <a href="{{ route('campus.help.categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nova Categoria
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Àrea</th>
                                        <th>Icona</th>
                                        <th>Ordre</th>
                                        <th>Articles</th>
                                        <th>Estat</th>
                                        <th>Accions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                    <tr>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                {{ ucfirst($category->area) }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="{{ $category->iconClass }} text-primary"></i>
                                        </td>
                                        <td>{{ $category->order ?? 0 }}</td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $category->articles->count() }} articles
                                            </span>
                                        </td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="badge bg-success">Activa</span>
                                            @else
                                                <span class="badge bg-danger">Inactiva</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('campus.help.categories.edit', $category) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('campus.help.categories.toggle-active', $category) }}" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-info" 
                                                            title="{{ $category->is_active ? 'Desactivar' : 'Activar' }}">
                                                        <i class="bi bi-{{ $category->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('campus.help.categories.destroy', $category) }}" 
                                                      class="d-inline" onsubmit="return confirm('Estàs segur de voler eliminar aquesta categoria?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                            @if($category->articles->count() > 0) disabled @endif>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-folder display-4 text-muted"></i>
                            <h5 class="mt-3">No s'han trobat categories</h5>
                            <p class="text-muted">Encara no s'han creat categories d'ajuda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
