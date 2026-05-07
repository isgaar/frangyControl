@extends('layouts.dashboard')

@php
    $isEdit = isset($cotizacion);
    $selectedServicio = old('servicio_id', $cotizacion->servicio_id ?? '');
@endphp

@section('title', $isEdit ? 'Editar cotización' : 'Nueva cotización')

@section('content')
    <div class="resource-page">
        <div class="page-header">
            <div>
                <span class="page-eyebrow">Cotizaciones</span>
                <h1 class="page-title">{{ $isEdit ? 'Editar cotización' : 'Nueva cotización' }}</h1>
            </div>
            <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-dark">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Revisa los campos antes de guardar.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="resource-panel">
            <form action="{{ $isEdit ? route('cotizaciones.update', $cotizacion->id_cotizacion) : route('cotizaciones.store') }}" method="post" id="quoteForm">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="cliente_id">Cliente</label>
                        <select class="form-control" id="cliente_id" name="cliente_id" required>
                            <option value="">Selecciona un cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id_cliente }}" {{ (int) old('cliente_id', $cotizacion->cliente_id ?? 0) === (int) $cliente->id_cliente ? 'selected' : '' }}>
                                    {{ $cliente->nombreCompleto }} · {{ $cliente->telefono }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="servicio_id">Servicio</label>
                        <select class="form-control" id="servicio_id" name="servicio_id" required>
                            <option value="">Selecciona un servicio</option>
                            @foreach ($servicios as $servicio)
                                <option value="{{ $servicio['id_servicio'] }}" {{ (int) $selectedServicio === (int) $servicio['id_servicio'] ? 'selected' : '' }}>
                                    {{ $servicio['nombreServicio'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="precio_base">Precio exacto</label>
                        <input class="form-control" id="precio_base" name="precio_base" type="number" min="0" step="0.01" required value="{{ old('precio_base', $cotizacion->precio_base ?? 0) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="descuento_porcentaje">Rebaja %</label>
                        <input class="form-control" id="descuento_porcentaje" name="descuento_porcentaje" type="number" min="0" max="100" step="0.01" value="{{ old('descuento_porcentaje', $cotizacion->descuento_porcentaje ?? 0) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="vigencia">Vigencia</label>
                        <input class="form-control" id="vigencia" name="vigencia" type="date" value="{{ old('vigencia', isset($cotizacion) && $cotizacion->vigencia ? $cotizacion->vigencia->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="estado">Estado</label>
                        <select class="form-control" id="estado" name="estado" required>
                            @foreach ($estados as $value => $label)
                                <option value="{{ $value }}" {{ old('estado', $cotizacion->estado ?? 'borrador') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="user_id">Responsable</label>
                        <select class="form-control" id="user_id" name="user_id">
                            <option value="">Usuario actual</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ (int) old('user_id', $cotizacion->user_id ?? 0) === (int) $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="notas">Notas</label>
                        <textarea class="form-control" id="notas" name="notas" rows="4">{{ old('notas', $cotizacion->notas ?? '') }}</textarea>
                    </div>
                </div>

                <div class="metrics-grid mt-4">
                    <article class="metric-card">
                        <span class="metric-card__label">Subtotal</span>
                        <p class="metric-card__value" id="quoteSubtotal">$0.00</p>
                        <p class="metric-card__copy">Precio antes de rebaja</p>
                    </article>
                    <article class="metric-card">
                        <span class="metric-card__label">Rebaja</span>
                        <p class="metric-card__value" id="quoteDiscount">$0.00</p>
                        <p class="metric-card__copy">Descuento aplicado</p>
                    </article>
                    <article class="metric-card">
                        <span class="metric-card__label">Total</span>
                        <p class="metric-card__value" id="quoteTotal">$0.00</p>
                        <p class="metric-card__copy">Importe final</p>
                    </article>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-dark">Cancelar</a>
                    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Actualizar cotización' : 'Guardar cotización' }}</button>
                </div>
            </form>
        </section>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var servicios = @json($servicios);
            var servicioSelect = document.getElementById('servicio_id');
            var precioInput = document.getElementById('precio_base');
            var descuentoInput = document.getElementById('descuento_porcentaje');
            var subtotal = document.getElementById('quoteSubtotal');
            var discount = document.getElementById('quoteDiscount');
            var total = document.getElementById('quoteTotal');
            var editing = @json($isEdit);

            function money(value) {
                return Number(value || 0).toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
            }

            function recalculate() {
                var price = Number(precioInput.value || 0);
                var percent = Number(descuentoInput.value || 0);
                var discountValue = price * (percent / 100);

                subtotal.textContent = money(price);
                discount.textContent = money(discountValue);
                total.textContent = money(Math.max(price - discountValue, 0));
            }

            servicioSelect.addEventListener('change', function () {
                var selected = servicios.find(function (servicio) {
                    return String(servicio.id_servicio) === String(servicioSelect.value);
                });

                if (!selected) return;

                precioInput.value = selected.precio_base || 0;
                descuentoInput.value = selected.descuento_activo ? selected.descuento_porcentaje : 0;
                recalculate();
            });

            precioInput.addEventListener('input', recalculate);
            descuentoInput.addEventListener('input', recalculate);

            if (!editing && servicioSelect.value) {
                servicioSelect.dispatchEvent(new Event('change'));
            }

            recalculate();
        });
    </script>
@endsection
