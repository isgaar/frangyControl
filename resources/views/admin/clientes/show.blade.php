@extends('layouts.dashboard')

@section('title', 'Detalle de cliente')

@section('content')
    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Directorio</span>
                    <h1 class="resource-hero__title">{{ $cliente->nombreCompleto }}</h1>
                    <p>Consulta la información guardada del cliente antes de editar o regresar al directorio.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('clientes.edit', $cliente->id_cliente) }}" class="btn btn-primary">
                        <i class="fas fa-user-edit me-1"></i> Editar
                    </a>
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
            </div>
        </section>

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Datos de contacto</span>
                    <h2 class="resource-panel__title">Ficha del cliente</h2>
                    <p class="resource-panel__copy">Información disponible para órdenes y seguimiento.</p>
                </div>
            </div>

            <div class="resource-kv mt-4">
                <div class="resource-kv__item">
                    <span class="resource-kv__label">Nombre completo</span>
                    <p class="resource-kv__value">{{ $cliente->nombreCompleto }}</p>
                </div>
                <div class="resource-kv__item">
                    <span class="resource-kv__label">Teléfono</span>
                    <p class="resource-kv__value">{{ $cliente->telefono }}</p>
                </div>
                <div class="resource-kv__item">
                    <span class="resource-kv__label">Correo electrónico</span>
                    <p class="resource-kv__value">{{ $cliente->correo }}</p>
                </div>
                <div class="resource-kv__item">
                    <span class="resource-kv__label">RFC</span>
                    <p class="resource-kv__value">{{ $cliente->rfc ?: 'Sin RFC' }}</p>
                </div>
            </div>
        </section>
    </div>
@endsection
