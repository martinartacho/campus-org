@extends('campus.shared.layout')

@section('title', 'Editar Categoria d\'Ajuda')
@section('subtitle', 'Editar categoria existent')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Editar Categoria d\'Ajuda</h1>
                <a href="{{ route('campus.help.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Tornar
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('campus.help.categories.update', $helpCategory) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="{{ old('name', $helpCategory->name) }}" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Àrea *</label>
                                    <select name="area" class="form-select" required>
                                        <option value="">Seleccionar àrea</option>
                                        @foreach($areas as $key => $label)
                                            <option value="{{ $key }}" {{ old('area', $helpCategory->area) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('area')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Icona *</label>
                                    <input type="text" name="icon" class="form-control" 
                                           value="{{ old('icon', $helpCategory->icon) }}" placeholder="bi bi-question-circle" required>
                                    <small class="form-text">Usa classes de Bootstrap Icons (ex: bi bi-question-circle)</small>
                                    @error('icon')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Ordre</label>
                                    <input type="number" name="order" class="form-control" 
                                           value="{{ old('order', $helpCategory->order) }}" min="0">
                                    @error('order')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Estat</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                               value="1" {{ old('is_active', $helpCategory->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label">Categoria activa</label>
                                    </div>
                                    @error('is_active')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('campus.help.categories.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>
                                Cancel·lar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Actualitzar Categoria
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
