@extends('layouts.dashboard')

@php
    $isEdit = isset($cotizacion);
    $selectedServicio = old('servicio_id', $cotizacion->servicio_id ?? '');
    $conceptosCotizacion = old('conceptos');

    if (is_null($conceptosCotizacion)) {
        $conceptosCotizacion = $isEdit
            ? $cotizacion->conceptos->map(fn ($concepto) => [
                'tipo' => $concepto->tipo,
                'descripcion' => $concepto->descripcion,
                'cantidad' => $concepto->cantidad,
                'precio_unitario' => $concepto->precio_unitario,
            ])->values()->all()
            : [];
    }
@endphp

@section('title', $isEdit ? 'Editar cotización' : 'Nueva cotización')

@section('content')
    <style>
        .quote-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, 360px);
            gap: 1rem;
            align-items: start;
        }

        .quote-main {
            display: grid;
            gap: 1rem;
            min-width: 0;
        }

        .quote-section {
            border: 1px solid var(--dashboard-border);
            border-radius: 12px;
            background: var(--dashboard-surface);
            padding: 1rem;
        }

        .quote-section__header {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            margin-bottom: 1rem;
        }

        .quote-section__step {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--dashboard-border);
            font-weight: 800;
            color: var(--dashboard-text);
            background: var(--dashboard-surface-soft);
        }

        .quote-section__title {
            margin: 0;
            font-weight: 800;
            color: var(--dashboard-text);
        }

        .quote-section__copy {
            margin: .15rem 0 0;
            color: var(--dashboard-muted);
            font-size: .88rem;
        }

        .quote-summary {
            position: sticky;
            top: 1rem;
            display: grid;
            gap: .75rem;
        }

        .quote-summary-card {
            border: 1px solid var(--dashboard-border);
            border-radius: 12px;
            background: var(--dashboard-surface);
            padding: 1rem;
        }

        .quote-summary-card__title {
            margin: 0 0 .75rem;
            font-weight: 800;
            color: var(--dashboard-text);
        }

        .quote-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .65rem 0;
            border-bottom: 1px solid var(--dashboard-border);
        }

        .quote-total-row:last-child {
            border-bottom: 0;
        }

        .quote-total-row span {
            color: var(--dashboard-muted);
            font-weight: 700;
        }

        .quote-total-row strong {
            color: var(--dashboard-text);
            font-weight: 800;
        }

        .quote-total-row.is-final strong {
            font-size: 1.35rem;
        }

        .quote-service-preview {
            display: grid;
            gap: .35rem;
            padding: .75rem;
            border: 1px solid var(--dashboard-border);
            border-radius: 10px;
            background: var(--dashboard-surface-soft);
        }

        .quote-service-preview p {
            margin: 0;
            color: var(--dashboard-text);
            font-weight: 800;
        }

        .quote-service-preview span {
            color: var(--dashboard-muted);
            font-size: .86rem;
        }

        .quote-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .quote-date-shortcuts {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .4rem;
            margin-top: .45rem;
        }

        .quote-date-shortcuts .btn {
            min-height: 32px;
            padding-block: .25rem;
        }

        .quote-items {
            display: grid;
            gap: .75rem;
        }

        .quote-item-row {
            display: grid;
            grid-template-columns: minmax(150px, .9fr) minmax(220px, 1.4fr) minmax(92px, .45fr) minmax(120px, .55fr) minmax(120px, .55fr) 42px;
            gap: .5rem;
            align-items: end;
            padding: .75rem;
            border: 1px solid var(--dashboard-border);
            border-radius: 12px;
            background: var(--dashboard-surface-soft);
        }

        .quote-item-row__total {
            min-height: 38px;
            display: flex;
            align-items: center;
            font-weight: 800;
            color: var(--dashboard-text);
        }

        .quote-item-remove {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .quote-items-empty {
            padding: .85rem;
            border: 1px dashed var(--dashboard-border);
            border-radius: 12px;
            color: var(--dashboard-muted);
            background: var(--dashboard-surface-soft);
        }

        .quote-field.form-control,
        .quote-field.form-select,
        .quote-field-addon.input-group-text {
            border-color: var(--dashboard-border);
            background-color: var(--dashboard-surface);
            color: var(--dashboard-text);
        }

        .quote-field.form-control:focus,
        .quote-field.form-select:focus {
            border-color: var(--dashboard-primary);
            background-color: var(--dashboard-surface);
            color: var(--dashboard-text);
            box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--dashboard-primary) 22%, transparent);
        }

        .quote-field.form-control::placeholder {
            color: var(--dashboard-muted);
        }

        html[data-theme="dark"] .quote-field.form-control,
        html[data-theme="dark"] .quote-field.form-select,
        html[data-theme="dark"] .quote-field-addon.input-group-text {
            border-color: var(--dashboard-border);
            background-color: var(--dashboard-surface-soft);
            color: var(--dashboard-text);
        }

        html[data-theme="dark"] .quote-field.form-control:focus,
        html[data-theme="dark"] .quote-field.form-select:focus {
            background-color: var(--dashboard-surface-soft);
            color: var(--dashboard-text);
        }

        .quote-field.form-select option {
            background-color: var(--dashboard-surface);
            color: var(--dashboard-text);
        }

        html[data-theme="dark"] .quote-field.form-select option {
            background-color: var(--dashboard-surface-soft);
            color: var(--dashboard-text);
        }

        @media (max-width: 992px) {
            .quote-layout {
                grid-template-columns: 1fr;
            }

            .quote-summary {
                position: static;
            }
        }

        @media (max-width: 1200px) {
            .quote-item-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .quote-item-remove {
                width: 100%;
            }
        }
    </style>

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

        <form action="{{ $isEdit ? route('cotizaciones.update', $cotizacion->id_cotizacion) : route('cotizaciones.store') }}" method="post" id="quoteForm">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="quote-layout">
                <div class="quote-main">
                    <section class="quote-section">
                        <div class="quote-section__header">
                            <span class="quote-section__step">1</span>
                            <div>
                                <h2 class="quote-section__title">Servicio y precio</h2>
                                <p class="quote-section__copy">El precio se llena desde el catálogo y puedes ajustarlo antes de guardar.</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="servicio_id">Servicio</label>
                                <select class="form-select quote-field" id="servicio_id" name="servicio_id" required>
                                    <option value="">Selecciona un servicio</option>
                                    @foreach ($servicios as $servicio)
                                        <option value="{{ $servicio['id_servicio'] }}" {{ (int) $selectedServicio === (int) $servicio['id_servicio'] ? 'selected' : '' }}>
                                            {{ $servicio['nombreServicio'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <div class="quote-service-preview" id="quoteServicePreview">
                                    <p>Servicio no seleccionado</p>
                                    <span>Al elegir un servicio se mostrarán su precio base y rebaja activa.</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="precio_base">Precio exacto</label>
                                <div class="input-group">
                                    <span class="input-group-text quote-field-addon">$</span>
                                    <input class="form-control quote-field" id="precio_base" name="precio_base" type="number" min="0" step="0.01" required value="{{ old('precio_base', $cotizacion->precio_base ?? 0) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="descuento_porcentaje">Rebaja %</label>
                                <div class="input-group">
                                    <input class="form-control quote-field" id="descuento_porcentaje" name="descuento_porcentaje" type="number" min="0" max="100" step="0.01" value="{{ old('descuento_porcentaje', $cotizacion->descuento_porcentaje ?? 0) }}">
                                    <span class="input-group-text quote-field-addon">%</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="vigencia">Vigencia</label>
                                <input class="form-control quote-field" id="vigencia" name="vigencia" type="date" value="{{ old('vigencia', isset($cotizacion) && $cotizacion->vigencia ? $cotizacion->vigencia->format('Y-m-d') : '') }}">
                                <div class="quote-date-shortcuts" aria-label="Atajos de vigencia">
                                    <button class="btn btn-outline-dark btn-sm" type="button" data-days="7">7 días</button>
                                    <button class="btn btn-outline-dark btn-sm" type="button" data-days="15">15 días</button>
                                    <button class="btn btn-outline-dark btn-sm" type="button" data-days="30">30 días</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="quote-section">
                        <div class="quote-section__header">
                            <span class="quote-section__step">2</span>
                            <div>
                                <h2 class="quote-section__title">Conceptos adicionales</h2>
                                <p class="quote-section__copy">Agrega refacciones, inventario, mano de obra o servicios extra que también deban cotizarse.</p>
                            </div>
                        </div>

                        <div class="quote-items" id="quoteItems">
                            <div class="quote-items-empty {{ count($conceptosCotizacion) ? 'd-none' : '' }}" id="quoteItemsEmpty">
                                Sin conceptos adicionales. Puedes dejar solo el servicio principal o agregar partidas.
                            </div>

                            <div id="quoteItemsRows">
                                @foreach ($conceptosCotizacion as $index => $concepto)
                                    <div class="quote-item-row" data-quote-item>
                                        <div>
                                            <label class="form-label" for="concepto_tipo_{{ $index }}">Tipo</label>
                                            <select class="form-select quote-field" id="concepto_tipo_{{ $index }}" name="conceptos[{{ $index }}][tipo]">
                                                @foreach ($tiposConcepto as $value => $label)
                                                    <option value="{{ $value }}" {{ ($concepto['tipo'] ?? 'inventario') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label" for="concepto_descripcion_{{ $index }}">Descripción</label>
                                            <input class="form-control quote-field" id="concepto_descripcion_{{ $index }}" name="conceptos[{{ $index }}][descripcion]" type="text" maxlength="180" value="{{ $concepto['descripcion'] ?? '' }}">
                                        </div>
                                        <div>
                                            <label class="form-label" for="concepto_cantidad_{{ $index }}">Cantidad</label>
                                            <input class="form-control quote-field" id="concepto_cantidad_{{ $index }}" name="conceptos[{{ $index }}][cantidad]" type="number" min="0.01" step="0.01" value="{{ $concepto['cantidad'] ?? 1 }}" data-item-quantity>
                                        </div>
                                        <div>
                                            <label class="form-label" for="concepto_precio_{{ $index }}">Precio</label>
                                            <input class="form-control quote-field" id="concepto_precio_{{ $index }}" name="conceptos[{{ $index }}][precio_unitario]" type="number" min="0" step="0.01" value="{{ $concepto['precio_unitario'] ?? 0 }}" data-item-price>
                                        </div>
                                        <div>
                                            <span class="form-label d-block">Subtotal</span>
                                            <span class="quote-item-row__total" data-item-total>$0.00</span>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger quote-item-remove" data-remove-item title="Quitar concepto">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <div>
                                <button class="btn btn-outline-dark btn-sm" type="button" id="addQuoteItem">
                                    <i class="fas fa-plus me-1"></i> Agregar concepto
                                </button>
                            </div>
                        </div>
                    </section>

                    <section class="quote-section">
                        <div class="quote-section__header">
                            <span class="quote-section__step">3</span>
                            <div>
                                <h2 class="quote-section__title">Seguimiento</h2>
                                <p class="quote-section__copy">Define el estado comercial y agrega notas internas o condiciones.</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="estado">Estado</label>
                                <select class="form-select quote-field" id="estado" name="estado" required>
                                    @foreach ($estados as $value => $label)
                                        <option value="{{ $value }}" {{ old('estado', $cotizacion->estado ?? 'borrador') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="user_id">Responsable</label>
                                <select class="form-select quote-field" id="user_id" name="user_id">
                                    <option value="">Usuario actual</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ (int) old('user_id', $cotizacion->user_id ?? 0) === (int) $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="notas">Notas</label>
                                <textarea class="form-control quote-field" id="notas" name="notas" rows="4" maxlength="2000" placeholder="Condiciones, alcance del servicio o detalles para seguimiento.">{{ old('notas', $cotizacion->notas ?? '') }}</textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="quote-summary">
                    <section class="quote-summary-card">
                        <h2 class="quote-summary-card__title">Resumen</h2>
                        <div class="quote-total-row">
                            <span>Subtotal</span>
                            <strong id="quoteSubtotal">$0.00</strong>
                        </div>
                        <div class="quote-total-row">
                            <span>Rebaja</span>
                            <strong id="quoteDiscount">$0.00</strong>
                        </div>
                        <div class="quote-total-row">
                            <span>Conceptos extra</span>
                            <strong id="quoteItemsTotal">$0.00</strong>
                        </div>
                        <div class="quote-total-row is-final">
                            <span>Total</span>
                            <strong id="quoteTotal">$0.00</strong>
                        </div>
                    </section>

                    <section class="quote-summary-card">
                        <h2 class="quote-summary-card__title">Antes de guardar</h2>
                        <div class="quote-total-row">
                            <span>Servicio</span>
                            <strong id="quoteSummaryService">Pendiente</strong>
                        </div>
                        <div class="quote-total-row">
                            <span>Vigencia</span>
                            <strong id="quoteSummaryVigencia">Sin vigencia</strong>
                        </div>
                    </section>

                    <div class="quote-actions">
                        <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-dark">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> {{ $isEdit ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </aside>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var servicios = @json($servicios);
            var servicioSelect = document.getElementById('servicio_id');
            var precioInput = document.getElementById('precio_base');
            var descuentoInput = document.getElementById('descuento_porcentaje');
            var vigenciaInput = document.getElementById('vigencia');
            var subtotal = document.getElementById('quoteSubtotal');
            var discount = document.getElementById('quoteDiscount');
            var itemsTotal = document.getElementById('quoteItemsTotal');
            var total = document.getElementById('quoteTotal');
            var servicePreview = document.getElementById('quoteServicePreview');
            var summaryService = document.getElementById('quoteSummaryService');
            var summaryVigencia = document.getElementById('quoteSummaryVigencia');
            var addQuoteItem = document.getElementById('addQuoteItem');
            var quoteItemsRows = document.getElementById('quoteItemsRows');
            var quoteItemsEmpty = document.getElementById('quoteItemsEmpty');
            var editing = @json($isEdit);
            var initialServicio = String(@json($selectedServicio));
            var tiposConcepto = @json($tiposConcepto);
            var nextItemIndex = quoteItemsRows ? quoteItemsRows.querySelectorAll('[data-quote-item]').length : 0;

            function money(value) {
                return Number(value || 0).toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
            }

            function selectedService() {
                return servicios.find(function (servicio) {
                    return String(servicio.id_servicio) === String(servicioSelect.value);
                });
            }

            function updateServicePreview() {
                var service = selectedService();

                if (!service) {
                    servicePreview.querySelector('p').textContent = 'Servicio no seleccionado';
                    servicePreview.querySelector('span').textContent = 'Al elegir un servicio se mostrarán su precio base y rebaja activa.';
                    summaryService.textContent = 'Pendiente';
                    return;
                }

                servicePreview.querySelector('p').textContent = service.nombreServicio;
                servicePreview.querySelector('span').textContent = 'Catálogo: ' + money(service.precio_base) + ' · Rebaja activa: ' + (service.descuento_activo ? service.descuento_porcentaje + '%' : '0%');
                summaryService.textContent = service.nombreServicio;
            }

            function updateVigenciaSummary() {
                if (!vigenciaInput.value) {
                    summaryVigencia.textContent = 'Sin vigencia';
                    return;
                }

                var parts = vigenciaInput.value.split('-');
                summaryVigencia.textContent = parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : vigenciaInput.value;
            }

            function recalculate() {
                var price = Number(precioInput.value || 0);
                var percent = Math.min(Math.max(Number(descuentoInput.value || 0), 0), 100);
                var discountValue = price * (percent / 100);
                var extraTotal = calculateItemsTotal();

                subtotal.textContent = money(price);
                discount.textContent = money(discountValue);
                itemsTotal.textContent = money(extraTotal);
                total.textContent = money(Math.max(price - discountValue + extraTotal, 0));
            }

            function calculateItemsTotal() {
                if (!quoteItemsRows) {
                    return 0;
                }

                return Array.from(quoteItemsRows.querySelectorAll('[data-quote-item]')).reduce(function (sum, row) {
                    var quantity = Number(row.querySelector('[data-item-quantity]').value || 0);
                    var price = Number(row.querySelector('[data-item-price]').value || 0);
                    var rowTotal = Math.max(quantity, 0) * Math.max(price, 0);
                    row.querySelector('[data-item-total]').textContent = money(rowTotal);

                    return sum + rowTotal;
                }, 0);
            }

            function refreshItemsEmptyState() {
                if (!quoteItemsEmpty || !quoteItemsRows) {
                    return;
                }

                quoteItemsEmpty.classList.toggle('d-none', quoteItemsRows.querySelectorAll('[data-quote-item]').length > 0);
            }

            function tipoOptions(selected) {
                return Object.keys(tiposConcepto).map(function (value) {
                    return '<option value="' + value + '"' + (value === selected ? ' selected' : '') + '>' + tiposConcepto[value] + '</option>';
                }).join('');
            }

            function bindItemRow(row) {
                row.querySelectorAll('[data-item-quantity], [data-item-price]').forEach(function (input) {
                    input.addEventListener('input', recalculate);
                });

                row.querySelector('[data-remove-item]').addEventListener('click', function () {
                    row.remove();
                    refreshItemsEmptyState();
                    recalculate();
                });
            }

            function createItemRow() {
                var index = nextItemIndex++;
                var wrapper = document.createElement('div');
                wrapper.className = 'quote-item-row';
                wrapper.dataset.quoteItem = 'true';
                wrapper.innerHTML =
                    '<div>' +
                        '<label class="form-label" for="concepto_tipo_' + index + '">Tipo</label>' +
                        '<select class="form-select quote-field" id="concepto_tipo_' + index + '" name="conceptos[' + index + '][tipo]">' + tipoOptions('inventario') + '</select>' +
                    '</div>' +
                    '<div>' +
                        '<label class="form-label" for="concepto_descripcion_' + index + '">Descripción</label>' +
                        '<input class="form-control quote-field" id="concepto_descripcion_' + index + '" name="conceptos[' + index + '][descripcion]" type="text" maxlength="180">' +
                    '</div>' +
                    '<div>' +
                        '<label class="form-label" for="concepto_cantidad_' + index + '">Cantidad</label>' +
                        '<input class="form-control quote-field" id="concepto_cantidad_' + index + '" name="conceptos[' + index + '][cantidad]" type="number" min="0.01" step="0.01" value="1" data-item-quantity>' +
                    '</div>' +
                    '<div>' +
                        '<label class="form-label" for="concepto_precio_' + index + '">Precio</label>' +
                        '<input class="form-control quote-field" id="concepto_precio_' + index + '" name="conceptos[' + index + '][precio_unitario]" type="number" min="0" step="0.01" value="0" data-item-price>' +
                    '</div>' +
                    '<div>' +
                        '<span class="form-label d-block">Subtotal</span>' +
                        '<span class="quote-item-row__total" data-item-total>$0.00</span>' +
                    '</div>' +
                    '<button type="button" class="btn btn-outline-danger quote-item-remove" data-remove-item title="Quitar concepto">' +
                        '<i class="fas fa-times"></i>' +
                    '</button>';

                quoteItemsRows.appendChild(wrapper);
                bindItemRow(wrapper);
                refreshItemsEmptyState();
                recalculate();
            }

            servicioSelect.addEventListener('change', function () {
                var service = selectedService();

                if (service && (!editing || String(servicioSelect.value) !== initialServicio)) {
                    precioInput.value = service.precio_base || 0;
                    descuentoInput.value = service.descuento_activo ? service.descuento_porcentaje : 0;
                }

                updateServicePreview();
                recalculate();
            });

            precioInput.addEventListener('input', recalculate);
            descuentoInput.addEventListener('input', recalculate);
            vigenciaInput.addEventListener('change', updateVigenciaSummary);
            addQuoteItem.addEventListener('click', createItemRow);

            quoteItemsRows.querySelectorAll('[data-quote-item]').forEach(bindItemRow);

            document.querySelectorAll('[data-days]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var date = new Date();
                    date.setDate(date.getDate() + Number(button.dataset.days || 0));
                    vigenciaInput.value = [
                        date.getFullYear(),
                        String(date.getMonth() + 1).padStart(2, '0'),
                        String(date.getDate()).padStart(2, '0')
                    ].join('-');
                    updateVigenciaSummary();
                });
            });

            if (!editing && servicioSelect.value) {
                servicioSelect.dispatchEvent(new Event('change'));
            }

            updateServicePreview();
            updateVigenciaSummary();
            recalculate();
        });
    </script>
@endsection
