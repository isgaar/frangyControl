@extends('layouts.dashboard')

@section('title', 'Nuevo Producto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Agregar al Inventario</h1>
    <a href="{{ route('inventario.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('inventario.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="codigo_barras" class="form-label">Código de Barras</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="codigo_barras" name="codigo_barras" value="{{ old('codigo_barras') }}">
                            <button class="btn btn-outline-primary" type="button" id="btn-scan">
                                <i class="fas fa-camera"></i> Escanear
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de Refacción / Producto *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required value="{{ old('nombre') }}">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="2">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="costo" class="form-label">Costo (Compra) *</label>
                            <input type="number" step="0.01" class="form-control" id="costo" name="costo" required value="{{ old('costo', 0) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="precio_venta" class="form-label">Precio Público *</label>
                            <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" required value="{{ old('precio_venta', 0) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cantidad_stock" class="form-label">Cantidad Inicial (Stock) *</label>
                            <input type="number" class="form-control" id="cantidad_stock" name="cantidad_stock" required value="{{ old('cantidad_stock', 1) }}">
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Panel de Escáner -->
    <div class="col-md-4" id="scanner-panel" style="display: none;">
        <div class="card shadow border-left-primary">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Cámara / Escáner</h6>
                <button type="button" class="btn-close" aria-label="Close" id="btn-close-scan"></button>
            </div>
            <div class="card-body">
                <div id="reader" width="100%"></div>
                <p class="text-muted small mt-2 text-center">Apunta la cámara al código de barras. Se registrará automáticamente.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnScan = document.getElementById('btn-scan');
        const btnCloseScan = document.getElementById('btn-close-scan');
        const scannerPanel = document.getElementById('scanner-panel');
        const inputCodigo = document.getElementById('codigo_barras');
        let html5QrcodeScanner = null;

        function onScanSuccess(decodedText, decodedResult) {
            inputCodigo.value = decodedText;
            html5QrcodeScanner.clear();
            scannerPanel.style.display = 'none';
        }

        function onScanFailure(error) {
            // Ignorar errores continuos de lectura
        }

        btnScan.addEventListener('click', function() {
            scannerPanel.style.display = 'block';
            if(!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 150} }, /* verbose= */ false);
            }
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        });

        btnCloseScan.addEventListener('click', function() {
            if(html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }
            scannerPanel.style.display = 'none';
        });
    });
</script>
@endsection
