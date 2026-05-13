@extends('layouts.dashboard')

@section('title', 'Eliminar cliente')

@section('content')
    <div class="resource-page">
        <section class="resource-confirm-card">
            <div class="resource-confirm-card__header">
                <div>
                    <span class="resource-form-card__eyebrow">Acción sensible</span>
                    <h1 class="resource-confirm-card__title">Eliminar cliente</h1>
                    <p class="resource-confirm-card__copy">Confirma que quieres retirar este contacto del directorio.</p>
                </div>
            </div>

            <form action="{{ route('clientes.destroy', $cliente->id_cliente) }}" method="GET">
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
                        <span class="resource-kv__label">Correo</span>
                        <p class="resource-kv__value">{{ $cliente->correo }}</p>
                    </div>
                </div>

                <div class="resource-warning mt-4">
                    <strong>Importante</strong>
                    Si este cliente ya tiene órdenes registradas, revisa primero que la eliminación no afecte el historial operativo.
                </div>

                <div class="resource-confirm-card__footer">
                    <div class="resource-footer-actions">
                        <a href="{{ route('clientes.index') }}" class="btn btn-outline-dark">Cancelar</a>
                        <button type="submit" class="btn btn-danger">Eliminar cliente</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
