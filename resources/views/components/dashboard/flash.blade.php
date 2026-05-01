@php
    $status = session('status');
    $statusType = session('status_type');

    $tone = match ($statusType) {
        'success' => 'success',
        'warning' => 'warning',
        'error', 'error-Query', 'danger' => 'danger',
        default => 'info',
    };

    $icon = match ($tone) {
        'success' => 'fas fa-check-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'danger' => 'fas fa-times-circle',
        default => 'fas fa-info-circle',
    };
@endphp

@if ($status)
    <div class="alert alert-{{ $tone }} dashboard-alert is-{{ $tone }}" role="alert" data-dashboard-alert>
        <div class="dashboard-alert__icon">
            <i class="{{ $icon }}"></i>
        </div>

        <div class="dashboard-alert__content">
            <strong>{{ $status }}</strong>
        </div>

        <button type="button" class="btn-close dashboard-alert__close" data-dashboard-dismiss aria-label="Cerrar mensaje">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger dashboard-alert is-danger" role="alert" data-dashboard-alert>
        <div class="dashboard-alert__icon">
            <i class="fas fa-times-circle"></i>
        </div>

        <div class="dashboard-alert__content">
            <strong>Hay campos que necesitan revisión.</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

        <button type="button" class="btn-close dashboard-alert__close" data-dashboard-dismiss aria-label="Cerrar mensaje">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif
