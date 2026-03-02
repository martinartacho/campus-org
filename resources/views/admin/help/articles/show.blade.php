@extends('campus.shared.layout')

@section('title', 'Mostrar Article d\'Ajuda')
@section('subtitle', 'Vista prèvia de l\'article')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ $helpArticle->title }}</h1>
                <div>
                    <a href="{{ route('campus.help.articles.edit', $helpArticle) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>
                        Editar
                    </a>
                    <a href="{{ route('campus.help.articles.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Tornar
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Àrea:</strong> 
                            <span class="badge bg-info">{{ ucfirst($helpArticle->area) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Estat:</strong> 
                            @switch($helpArticle->status)
                                @case('draft')
                                    <span class="badge bg-secondary">Borrador</span>
                                    @break
                                @case('validated')
                                    <span class="badge bg-success">Validat</span>
                                    @break
                                @case('obsolete')
                                    <span class="badge bg-danger">Obsolet</span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    @if($helpArticle->context)
                    <div class="row mb-4">
                        <div class="col-12">
                            <strong>Context:</strong> {{ $helpArticle->context }}
                        </div>
                    </div>
                    @endif

                    @if($helpArticle->type)
                    <div class="row mb-4">
                        <div class="col-12">
                            <strong>Tipus:</strong> {{ $helpArticle->type }}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-12">
                            <strong>Contingut:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {!! $helpArticle->content !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <strong>Creat per:</strong> {{ $helpArticle->createdBy?->name ?? '-' }}<br>
                                <strong>Data creació:</strong> {{ $helpArticle->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <strong>Actualitzat per:</strong> {{ $helpArticle->updatedBy?->name ?? '-' }}<br>
                                <strong>Data actualització:</strong> {{ $helpArticle->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
