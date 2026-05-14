@extends('layouts.dashboard')

@section('title', 'Importar Base de Datos')

@section('content_header')
    @if (Session::has('status'))
        <div class="col-md-12 alert-section">
            <div class="alert alert-{{ Session::get('status_type') == 'success' ? 'success' : 'danger' }} dashboard-legacy-alert">
                <span class="dashboard-legacy-alert__text">
                    {{ Session::get('status') }}
                    @php Session::forget('status'); @endphp
                </span>
            </div>
        </div>
    @endif
@stop

@section('content')
    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Mantenimiento y Respaldo</span>
                    <h1 class="resource-hero__title">Importar Base de Datos SQL</h1>
                    <p>Sube un archivo de volcado (.sql) para integrar registros masivos al sistema.</p>
                </div>
            </div>
        </section>

        <section class="resource-content">
            <div class="row">
                <div class="col-lg-6">
                    <div class="dashboard-card shadow-sm mb-4">
                        <div class="dashboard-card__header pb-0 border-0">
                            <h5 class="dashboard-card__title mb-0">Carga Segura de SQL</h5>
                        </div>
                        <div class="dashboard-card__body">
                            <form action="{{ route('database.import.process') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="sql_file" class="form-label fw-bold">Selecciona tu archivo (.sql)</label>
                                    <input class="form-control @error('sql_file') is-invalid @enderror" type="file" id="sql_file" name="sql_file" accept=".sql" required>
                                    @error('sql_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle text-primary"></i> Tamaño máximo permitido: 50MB.
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('¿Estás seguro de que deseas importar este archivo? Esta acción modificará tu base de datos actual.')">
                                        <i class="fas fa-upload me-2"></i> Procesar Importación
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="alert alert-warning shadow-sm border-0 border-start border-4 border-warning">
                        <h5 class="alert-heading fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Aviso de Inteligencia y Compatibilidad</h5>
                        <p class="mb-2">El motor de base de datos actual de tu sistema es: <strong>{{ strtoupper($dbDriver) }}</strong>.</p>
                        <hr>
                        <p class="mb-2"><strong>Protección Activa (Transacciones):</strong></p>
                        <p class="small mb-3">
                            Este módulo cuenta con "Rollback" inteligente. Si tu archivo contiene un error de sintaxis, o si es un dialecto que no corresponde a <strong>{{ strtoupper($dbDriver) }}</strong> (por ejemplo, subir un SQL de Postgres a un MySQL), el sistema abortará por completo la ejecución y regresará todo a su estado original sin perder ni corromper tu información actual.
                        </p>
                        <p class="mb-2"><strong>Recomendaciones:</strong></p>
                        <ul class="small mb-0">
                            <li>Procura subir archivos que contengan sentencias <code>INSERT</code> de solo datos.</li>
                            <li>Evita archivos que contengan sentencias <code>DROP TABLE</code> si deseas conservar los registros previos de otras tablas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
