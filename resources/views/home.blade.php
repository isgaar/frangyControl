@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Panel de control</h1>
@stop


@section('content')
<style>
.morning {
    background-image: url('https://cdn.wallpapersafari.com/25/78/fHPIeb.png');
    background-size: cover;
    background-position: center;
}

.afternoon {
    background-image: url('https://images.hdqwalls.com/download/deer-forest-fox-sun-red-trees-birds-4k-30-1920x1080.jpg');
    background-size: cover;
    background-position: center;
}

.evening {
    background-image: url('https://i.pinimg.com/originals/f7/e8/6e/f7e86eb60f387760f268530e3f69eb50.png');
    background-size: cover;
    background-position: center;
}

.greeting-icon {
    width: 70px;
    height: 70px;
    margin-bottom: 10px;
}

.card.panels-card .hour {
    font-size: 0.8rem;
    margin-top: 0.3rem;
}
</style>
<?php
// Obtener la hora actual
$currentHour = date('H');

// Determinar el momento del día
if ($currentHour >= 5 && $currentHour < 12) {
    $timeOfDay = 'morning';
    $timeOfDayClass = 'morning';
} elseif ($currentHour >= 12 && $currentHour < 18) {
    $timeOfDay = 'afternoon';
    $timeOfDayClass = 'afternoon';
} else {
    $timeOfDay = 'evening';
    $timeOfDayClass = 'evening';
}

// Obtener el nombre de usuario
$username = auth()->user()->name ?? 'Usuario';

?>
<div class="col-12 p-1">
    <div class="card text-white {{ $timeOfDayClass }}">
        <div class="card-body pb-0">
            @if ($timeOfDay === 'morning')
            <img src="{{ asset('sun.svg') }}" alt="Sun" class="greeting-icon">
            <div class="d-flex justify-content-between">
                <p class="mb-0 hour"></p>
                <p class="mb-0 hour">Buenos días</p>
            </div>
        </div>
        <hr>
        <div class="card-body pt-0">
            <h5 class="card-title">{{ $username }}</h5>
            @elseif ($timeOfDay === 'afternoon')
            <img src="{{ asset('sun.svg') }}" alt="Sun" class="greeting-icon">
            <div class="d-flex justify-content-between">
                <p class="mb-0 hour"></p>
                <p class="mb-0 hour">Buenas tardes</p>
            </div>
        </div>
        <hr>
        <div class="card-body pt-0">
            <h5 class="card-title">{{ $username }}</h5>
            @else
            <img src="{{ asset('moon.svg') }}" alt="Moon" class="greeting-icon">
            <div class="d-flex justify-content-between">
                <p class="mb-0 hour"></p>
                <p class="mb-0 hour">Buenas noches</p>
            </div>
        </div>
        <hr>
        <div class="card-body pt-0">
            <h3 class="card-title">{{ $username }}</h3>
            @endif
        </div>
    </div>
</div>





@php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
@endphp

@if (strpos($userAgent, 'Firefox') === false)
<div class="card">
    <div class="card-body" style="background-color: #FF7139; color: #fff;">
        <h5 class="card-title">
            <img src="https://play-lh.googleusercontent.com/l6ftn6BTu7Kfe8OdE4Itrdw5bTRVO3F_mTZH8xDa-FHO4m-lZAXmz5GxkXTMhqcF_y0"
                alt="Firefox Logo" width="30" height="30">
            Utiliza Firefox para una mejor experiencia gráfica
        </h5>
        <p class="card-text">Se recomienda utilizar el navegador Mozilla Firefox para aprovechar al máximo las
            características visuales de este sitio.</p>
    </div>
</div>
@endif




<div class="card text-center">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" aria-current="true" href="#">Últimas ordenes</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <table class="table table-striped mb-0">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center">#</th>
                    <th scope="col" class="text-center">Estado</th>
                    <th scope="col" class="text-center">Cliente</th>
                    <th scope="col" class="text-center">Tipo de Servicio</th>
                    <th scope="col" class="text-center">Vehículo</th>
                    <th scope="col" class="text-center">Encargado/a</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach ($ordenes as $row)
                <tr>
                    <th scope="row">{{ $row->id_ordenes }}</th>
                    <td class="text-center
                            @if ($row->status == 'cancelada')
                                text-danger
                            @elseif ($row->status == 'finalizada')
                                text-success
                            @elseif ($row->status == 'en proceso')
                                text-info
                            @endif
                        ">{{ ucwords($row->status) }}</td>

                    <td class="text-center">{{ ucwords($row->cliente->nombreCompleto) }}</td>
                    <td class="text-center">{{ $row->servicio->nombreServicio }}</td>
                    <td class="text-center">{{ $row->vehiculo->marca }}</td>
                    <td class="text-center">{{ $row->user->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <a href="admin/orden/listado" class="small-box-footer">Ir al panel de órdenes<i
                    class="fas fa-arrow-circle-right"></i></a>
</div>

<div class="row">
    <div class="col-lg-3 col-6 ">
        <!-- Small Box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>Clientes</h3>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="admin/cliente/nuevos" class="small-box-footer">Ver a detalle <i
                    class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
console.log('Hi!');
</script>
@stop