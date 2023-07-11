<?php
date_default_timezone_set('America/Mexico_City');
?>
<style>
.card {
    font-family: Arial, sans-serif;
    font-size: 12px;
    width: 100%;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 10px;
}

.card-title {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
}

label {
    display: block;
    font-weight: bold;
}

input[type="text"],
textarea {
    width: 100%;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 10px;
    resize: none;
    /* Evitar que el usuario pueda redimensionar */
}

.input-group-append {
    display: inline-block;
    vertical-align: middle;
    margin-left: 5px;
}

.input-group-text {
    padding: 5px;
}

.form-group {
    margin-bottom: 10px;
}

.form-check label {
    font-weight: normal;
}

.left-column {
    float: left;
    width: 35%;
}

.right-column {
    float: right;
    width: 55%;
}

.limited-textarea {
    height: 19em;
    /* Mostrar solo 25 líneas */
}

.centered-form {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

.centered-form .form-group {
    width: 95%;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header .logo img {
    height: 60px;
}

.header .icon img {
    height: 40px;
}
</style>

<div class="header">
    <div class="logo">
        <img src="{{ public_path('franlogo.png') }}" alt="Logo">
        <div>{{ date('d/m/Y H:i') }}</div>
    </div>
</div>


<div class="card">
    <div class="row">
        <div class="left-column">
            <h3 class="card-title">Información del cliente</h3>

            <label for="nombreCompleto">Nombre completo</label>
            <input type="text" name="nombreCompleto" value="{{ $orden->cliente->nombreCompleto }}" disabled>

            <br>

            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" value="{{ $orden->cliente->telefono }}" disabled>

            <br>

            <label for="correo">Correo electrónico</label>
            <input type="text" name="correo" value="{{ $orden->cliente->correo }}" disabled>
        </div>

        <div class="right-column">
            <h3 class="card-title">Datos de la unidad</h3>

            <label for="vehiculo_id">Marca</label>
            <input type="text" name="marca" value="{{ $orden->vehiculo->marca }}" disabled>

            <br>

            <label for="tvehiculo_id">Tipo de Vehículo</label>
            <input type="text" name="tipoVehiculo" value="{{ $orden->tipoVehiculo->tipo }}" disabled>

            <br>

            <label for="modelo">Modelo</label>
            <input type="text" name="modelo" value="{{ $orden->modelo }}" disabled>

            <br>

            <label for="yearVehiculo">Año</label>
            <input type="text" name="yearVehiculo" value="{{ $orden->yearVehiculo }}" disabled>

            <br>

            <label for="color">Color</label>
            <input type="text" name="color" value="{{ $orden->color }}" disabled>

            <br>

            <label for="placas">Placas</label>
            <input type="text" name="placas" value="{{ $orden->placas }}" disabled>

            <br>

            <label for="kilometraje">Kilometraje</label>
            <input type="text" name="kilometraje" value="{{ $orden->kilometraje }} Km" disabled>

            <br>

            <label for="motor">Motor</label>
            <input type="text" name="motor" value="{{ $orden->motor }}" disabled>

            <br>

            <label for="cilindros">Cilindros</label>
            <input type="text" name="cilindros" value="{{ $orden->cilindros }}" disabled>

            <br>

            <label for="numSerie">No. Serie</label>
            <input type="text" name="noSerievehiculo" value="{{ $orden->noSerievehiculo }}">
        </div>

        <div style="clear: both;"></div>

        <h3 class="card-title">Datos de la orden</h3>

        <div class="centered-form">
            <div class="form-group">
                <label for="servicio_id">Tipo de Servicio</label>
                <input type="text" name="nombreServicio" value="{{ $orden->servicio->nombreServicio }}" disabled>
            </div>

            <div class="form-group">
                <label for="user_id">Engargado(a)</label>
                <input type="text" name="name" value="{{ $orden->user->name }}" disabled>
            </div>

            <div class="form-group">
                <label for="observacionesInt">Observaciones internas (Recepción)</label>
                <textarea name="observacionesInt" class="limited-textarea"
                    disabled>{{ $orden->observacionesInt }}</textarea>
            </div>

            <div class="form-group">
                <label for="recomendacionesCliente">Recomendaciones del cliente</label>
                <textarea name="recomendacionesCliente" class="limited-textarea"
                    disabled>{{ $orden->recomendacionesCliente }}</textarea>
            </div>

            <div class="form-group">
                <label for="detallesOrden">Detalles del servicio</label>
                <textarea name="detallesOrden" class="limited-textarea" disabled>{{ $orden->detallesOrden }}</textarea>
            </div>

            <div class="form-group">
                <label for="retiroRefacciones">Refacciones</label>
                <input type="text" name="retiroRefacciones"
                    value="{{ $orden->retiroRefacciones ? 'Retiró' : 'No retiró' }}" disabled>
            </div>

            <div class="form-group">
                <label for="fechaEntrega">Fecha de entrega</label>
                <?php
                $fechaEntrega = \Carbon\Carbon::createFromFormat('Y-m-d', $orden->fechaEntrega)->format('d/m/Y');
                ?>
                <input type="text" name="fechaEntrega" value="{{ $fechaEntrega }}" disabled>
            </div>

            <div class="form-group">
                <input type="checkbox" id="myCheckbox" disabled checked>
                <label for="myCheckbox">El cliente aceptó</label>
            </div>
        </div>
    </div>