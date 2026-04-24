@extends('layouts.dashboard')

@section('title', 'Perfil de usuario')

@section('content_header')
    @if (Session::has('status'))
        <div class="col-md-12 alert-section">
            <div class="alert alert-{{ Session::get('status_type') }} dashboard-legacy-alert">
                <span class="dashboard-legacy-alert__text">
                    {{ Session::get('status') }}
                    @php
                        Session::forget('status');
                    @endphp
                </span>
            </div>
        </div>
    @endif
@stop

@section('content')
    @php
        $roleName = optional($user->roles->first())->name ?? 'Sin privilegio';
        $initials = collect(preg_split('/\s+/', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
    @endphp

    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Consulta rápida</span>
                    <h1 class="resource-hero__title">Perfil del usuario</h1>
                    <p>Visualiza los datos clave del acceso para revisar identidad, correo y rol asignado sin entrar a edición.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a usuarios
                    </a>
                </div>
            </div>
        </section>

        <div class="resource-overview-grid">
            <section class="resource-person-card">
                <div class="resource-person-card__top">
                    <div class="resource-avatar">{{ $initials ?: 'U' }}</div>
                    <div>
                        <h2 class="resource-person-card__title">{{ $user->name }}</h2>
                        <p class="resource-person-card__copy">Cuenta administrativa registrada en el panel.</p>
                    </div>
                </div>

                <div class="resource-kv">
                    <div class="resource-kv__item">
                        <span class="resource-kv__label">Correo electrónico</span>
                        <p class="resource-kv__value">{{ $user->email }}</p>
                    </div>
                    <div class="resource-kv__item">
                        <span class="resource-kv__label">Rol actual</span>
                        <p class="resource-kv__value">{{ $roleName }}</p>
                    </div>
                    <div class="resource-kv__item">
                        <span class="resource-kv__label">Seguridad</span>
                        <p class="resource-kv__value">La contraseña no se muestra por seguridad.</p>
                    </div>
                </div>

                <div class="resource-person-card__footer">
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-dark">
                        <i class="fas fa-pen mr-1"></i> Editar usuario
                    </a>
                </div>
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Resumen</span>
                <h2 class="resource-form-card__title">Estado del acceso</h2>
                <p class="resource-side-card__copy">Esta vista es útil para confirmar rápidamente si el rol y el correo siguen correctos.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>El rol mostrado es el primero asignado al usuario dentro del sistema.</li>
                    <li>Si necesitas cambiar permisos, entra al modo edición desde este mismo panel.</li>
                    <li>La contraseña permanece oculta para evitar exposición de credenciales.</li>
                </ul>
            </aside>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection
