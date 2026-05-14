@extends('layouts.dashboard')

@section('title', 'Auditoría y Bitácora')

@section('content_header')
    @if (Session::has('status'))
        <div class="col-md-12 alert-section">
            <div class="alert alert-{{ Session::get('status_type') }} dashboard-legacy-alert">
                <span class="dashboard-legacy-alert__text">
                    {{ Session::get('status') }}
                    @php Session::forget('status'); @endphp
                </span>
            </div>
        </div>
    @endif
    @if (isset($message))
        <div class="col-md-12 alert-section">
            <div class="alert alert-warning dashboard-legacy-alert">
                <span class="dashboard-legacy-alert__text">{{ $message }}</span>
            </div>
        </div>
    @endif
@stop

@section('content')
    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Seguridad y Métricas</span>
                    <h1 class="resource-hero__title">Bitácora de Actividades</h1>
                    <p>Monitorea las acciones realizadas por los usuarios, inicios de sesión y creación de empleados.</p>
                </div>
            </div>

            <div class="resource-hero__toolbar">
                <div class="filter-toolbar">
                    <form action="{{ route('auditoria.index') }}" method="GET" class="d-flex align-items-center w-100 gap-2">
                        <div class="filter-search-box">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por usuario, acción o IP..." autocomplete="off">
                            <button type="submit" aria-label="Buscar"><i class="fas fa-search"></i></button>
                        </div>
                        <select name="limit" class="form-select w-auto" onchange="this.form.submit()">
                            <option value="15" {{ $limit == 15 ? 'selected' : '' }}>15 reg.</option>
                            <option value="30" {{ $limit == 30 ? 'selected' : '' }}>30 reg.</option>
                            <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 reg.</option>
                        </select>
                        @if($search !== '')
                            <a href="{{ route('auditoria.index') }}" class="btn btn-light filter-btn-clear">
                                <i class="fas fa-times-circle"></i> Limpiar
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </section>

        <section class="resource-table-card">
            <div class="table-responsive">
                <table class="table table-hover table-compact table-borderless align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="min-width: 150px;">FECHA Y HORA</th>
                            <th scope="col">USUARIO</th>
                            <th scope="col">ACCIÓN</th>
                            <th scope="col" style="min-width: 250px;">DESCRIPCIÓN</th>
                            <th scope="col">DIRECCIÓN IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $log)
                            <tr>
                                <td>
                                    <strong>{{ $log->created_at->format('d/m/Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div class="fw-bold">{{ $log->user->name }}</div>
                                        <div class="text-muted small">{{ $log->user->email }}</div>
                                    @else
                                        <span class="text-muted fst-italic">Sistema / Usuario eliminado</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ str_contains(strtolower($log->action), 'inicio') ? 'bg-success' : 'bg-primary' }} text-white px-2 py-1" style="font-size: 0.75rem;">
                                        {{ mb_strtoupper($log->action) }}
                                    </span>
                                </td>
                                <td>
                                    <p class="mb-0 text-truncate" style="max-width: 300px;" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </p>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 300px;" title="{{ $log->user_agent }}">
                                        <i class="fas fa-laptop text-black-50 pe-1"></i>{{ $log->user_agent }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary rounded-pill">{{ $log->ip_address ?? 'Desconocida' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fas fa-clipboard-list fa-3x"></i></div>
                                    <p class="mb-0 fw-semibold">No se encontraron registros de auditoría</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data->hasPages())
                <div class="resource-table-card__pagination">
                    {{ $data->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </div>
@stop
