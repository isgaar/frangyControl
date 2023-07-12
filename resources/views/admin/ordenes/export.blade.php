<?php
date_default_timezone_set('America/Mexico_City');
?>
<style>
.card {
    font-family: Arial, sans-serif;
    font-size: 12px;
    width: 100%;
    margin-bottom: 20px;
    border: 2px solid #ccc;
    border-radius: 4px;
    padding: 5px;
}

.form-value {
    margin-bottom: 6px;
    font-family: Arial, sans-serif;
}

.small-text {
    font-family: Arial, sans-serif;
    font-size: 8.2px;
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
    padding: 2px;
}

.form-group {
    margin-bottom: 10px;
}

.form-check label {
    font-weight: normal;
}

.form-checkbox-label {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.form-checkbox-label input[type="checkbox"] {
    margin-right: 5px;
}

.left-column {
    float: left;
    width: 50%;
    margin: 10px;
}

.right-column {
    float: right;
    width: 45%;
    margin: 10px;
}

.limited-textarea {
    height: 20em;
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

.date-row {
    display: flex;
    justify-content: space-between;
}

.left-col {
    flex: 1;
    text-align: left;
}

.right-col {
    flex: 1;
    text-align: right;
    margin: %;
}
</style>




<div class="card">
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('franlogo.png') }}" alt="Logo">
            <div>Fecha de exportación:
                {{ \Carbon\Carbon::now('America/Mexico_City')->locale('es_ES')->isoFormat('LLLL') }}</div>

        </div>
    </div>
    <div class="row">
    <label class="form-label" for="nombreCompleto">N0. Orden en sistema</label>
            <div class="form-value">{{ strtoupper($orden->id_ordenes) }}</div>
        <div class="left-column">
            <h3 class="card-title">Datos del propietario</h3>

            <label class="form-label" for="nombreCompleto">Nombre o razón social</label>
            <div class="form-value">{{ strtoupper($orden->cliente->nombreCompleto) }}</div>

            <label class="form-label" for="telefono">Teléfono</label>
            <div class="form-value">{{ strtoupper($orden->cliente->telefono) }}</div>

            <label class="form-label" for="correo">Correo electrónico</label>
            <div class="form-value">{{ $orden->cliente->correo }}</div>

            <label class="form-label" for="rfc">RFC</label>
            <div class="form-value">{{ strtoupper($orden->cliente->rfc) }}</div>
        </div>

        <div class="right-column">
            <h3 class="card-title">Datos de la unidad</h3>

            <label class="form-label" for="vehiculo_id">Marca</label>
            <div class="form-value">{{ strtoupper($orden->vehiculo->marca) }}</div>

            <label class="form-label" for="tvehiculo_id">Tipo de Vehículo</label>
            <div class="form-value">{{ strtoupper($orden->tipoVehiculo->tipo) }}</div>

            <label class="form-label" for="modelo">Línea</label>
            <div class="form-value">{{ strtoupper($orden->modelo) }}</div>

            <label class="form-label" for="yearVehiculo">Año</label>
            <div class="form-value">{{ $orden->yearVehiculo }}</div>

            <label class="form-label" for="color">Color</label>
            <div class="form-value">{{ strtoupper($orden->color) }}</div>

            <label class="form-label" for="placas">Placas</label>
            <div class="form-value">{{ strtoupper($orden->placas) }}</div>

            <label class="form-label" for="kilometraje">Kilometraje</label>
            <div class="form-value">{{ $orden->kilometraje }} KM</div>

            <label class="form-label" for="motor">Motor</label>
            <div class="form-value">{{ strtoupper($orden->motor) }}</div>

            <label class="form-label" for="cilindros">Cilindros</label>
            <div class="form-value">{{ strtoupper($orden->cilindros) }}</div>

            <label class="form-label" for="numSerie">No. Serie</label>
            <div class="form-value">{{ strtoupper($orden->noSerievehiculo) }}</div>
        </div>

        <div style="clear: both;"></div>

        <h3 class="card-title">Datos del servicio</h3>

        <div class="centered-form">
            <div class="form-group">
                <label class="form-label" for="servicio_id">Tipo de Servicio:</label>
                <div class="form-value">{{ strtoupper($orden->servicio->nombreServicio) }}</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="user_id">Encargado(a):</label>
                <div class="form-value">{{ strtoupper($orden->user->name) }}</div>
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
                <label class="form-label" for="retiroRefacciones">Refacciones:</label>
                <div class="form-value">{{ $orden->retiroRefacciones ? 'Retiró' : 'No retiró' }}</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="fechaEntrega">Fecha de entrega:</label>
                <div class="form-value">
                    {{ \Carbon\Carbon::parse($orden->fechaEntrega)->locale('es_ES')->isoFormat('LL') }}</div>
            </div>

            <div class="form-group">
                <label class="form-checkbox-label" for="myCheckbox">
                    <input type="checkbox" id="myCheckbox" disabled checked>
                    El cliente aceptó
                </label>
            </div>

            <div style="clear: both;">
                <div class="left-col">
                    <label>Fecha de registro:</label>
                    <span>{{ \Carbon\Carbon::parse($orden->created_at)->locale('es_ES')->isoFormat('LLLL') }}</span>
                </div>
                <div class="right-col">
                    <label>Última actualización:</label>
                    <span>{{ \Carbon\Carbon::parse($orden->updated_at)->locale('es_ES')->isoFormat('LLLL') }}</span>
                </div>

                <div class="footer">
                    <p class="small-text">
                        PRESTACIÓN DE SERVICIOS DE REPARACIÓN Y/O MANTENIMIENTO DE VEHÍCULOS QUE CELEBRAN POR UNA PARTE
                        LLANTERA Y MECÁNICA AUTOMOTRÍZ FRANGY COMO “EL PRESTADOR DEL SERVICIO” Y POR LA OTRA PARTE “EL
                        CONSUMIDOR”, CUYOS NOMBRES Y DATOS CONSTAN EN LA CARÁTULA DE ESTA ORDEN DE SERVICIO, TOMANDO EN
                        CUENTA LOS SIGUIENTES:
                        <br><br>
                        TERMINOS Y CONDICIONES
                        <br>
                        PRIMERA. EL PRESTADOR DEL SERVICIO realizará todas las operaciones y composturas descritas en la
                        presente ORDEN DE SERVICIO, solicitadas por EL CONSUMIDOR, a las que se someterá el vehículo
                        para obtener condiciones de funcionamiento de acuerdo al estado en que se encuentra. SEGUNDA. El
                        precio total de los servicios contratados se establece en el “presupuesto” que forma parte de la
                        presente y se describe en el anverso, el cual será pagado por EL CONSUMIDOR, de la siguiente
                        forma: Al momento de celebrar el presente contrato por concepto de anticipo la cantidad que se
                        indica y el resto en la fecha de entrega del vehículo o cuando reciba su unidad. Todo pago
                        efectuado por EL CONSUMIDOR deberá realizarse en el establecimiento del PRESTADOR DEL SERVICIO,
                        al contado y en moneda nacional o bien con tarjeta de débito, crédito o transferencia
                        electrónica. TERCERA. EL PRESTADOR DEL SERVICIO previo a la realización del servicio presentará
                        a EL CONSUMIDOR el presupuesto, en caso de presentar adicionales se le informará al cliente con
                        el fin de que sean aprobadas por el mismo. Una vez aprobado, se procederá a efectuar el servicio
                        solicitado. Los incrementos que resulten durante la reparación por costos no previsibles en
                        rubros específicos deberán ser autorizados por EL CONSUMIDOR, en forma escrita. El tiempo, que
                        transcurra para requisitar esta condición modificará la fecha de entrega. CUARTA: Para el caso
                        de que EL CONSUMIDOR, sea el que proporcione las refacciones la fecha de entrega cambiara en
                        tiempo de acuerdo a la rapidez con que el CLIENTE consiga las refacciones. QUINTA: EL PRESTADOR
                        DEL SERVICIO exclusivamente utilizará para los servicios objeto de este contrato, partes,
                        refacciones u otros materiales nuevos y apropiados para el vehículo, salvo que EL CONSUMIDOR
                        autorice expresamente se usen otras. Si EL PRESTADOR DEL SERVICIO lo autoriza, EL CONSUMIDOR
                        suministrará las partes, refacciones o materiales necesarios para la reparación y/o
                        mantenimiento del vehículo. En ambos casos, la autorización respectiva se hará constar en el
                        anverso de la presente. SEXTA.- EL PRESTADOR DEL SERVICIO hará entrega de las refacciones,
                        partes o piezas sustituidas en la reparación y/o mantenimiento del vehículo al momento de
                        entrega de éste, salvo en los siguientes casos: A) Cuando EL CONSUMIDOR, exprese lo contrario.
                        B) Las partes, refacciones o piezas sean cambiadas en uso de garantía; C) Se trate de residuos
                        considerados peligrosos de acuerdo con las disposiciones legales aplicables. SÉPTIMA.- EL
                        CONSUMIDOR, autoriza el uso del vehículo en zonas aledañas con un radio de 4 cuadras a la
                        redonda respecto al área del establecimiento para efectos de pruebas o verificación de las
                        reparaciones a efectuar o efectuadas, observando que el nivel de combustible de la unidad en
                        monitoreo, diagnóstico y reparación variará en proporción a las mismas en la evaluación del
                        correcto funcionamiento del vehículo. EL PRESTADOR DEL SERVICIO no se hace responsable por la
                        pérdida de objetos dejados en el interior del vehículo, salvo que estos hayan sido notificados y
                        puestos bajo su resguardo al momento de la recepción del vehículo.
                        <br><br>
                        NOTA: EN LA PARTE FRONTAL DEL PRESENTE CONTRATO SE ENCUENTRA EL APARTADO EN EL CUAL SE INDICARÁN
                        LAS OBSERVACIONES PERTINENTES A LA RECEPCIÓN DEL VEHÍCULO QUE SON ANOTADAS POR EL PERSONAL QUE
                        RECIBE LA UNIDAD Y SON NOTIFICADAS EN TIEMPO Y FORMA AL CLIENTE QUIEN TENDRÁ A BIEN RECONOCER.
                        POR TANTO EL CLIENTE ACEPTA LAS CONDICIONES EN LAS QUE DEJA SU VEHÍCULO DESLINDANDO DE CUALQUIER
                        RESPONSABILIDAD AL RESPECTO AL PRESTADOR DE SERVICIOS POR DESPERFECTOS, ABOYADURAS, FALTA DE
                        ACCESORIOS, BIRLOS, ETC.
                    </p>
                </div>
            </div>

        </div>
    </div>