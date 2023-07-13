@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Panel de control</h1>
@stop


@section('content')
<style>
.morning {
    background-color: #FFEB3B;
    /* Color amarillo claro para la mañana */
}

.afternoon {
    background-color: #FF9800;
    /* Color naranja para la tarde */
}

.evening {
    background-color: #263238;
    /* Color azul oscuro para la noche */
}

.greeting-icon {
    width: 50px;
    height: 50px;
    margin-bottom: 10px;
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

<div class="card text-white text-center {{ $timeOfDayClass }}">
    <div class="card-header">
        @if ($timeOfDay === 'morning')
            <img src="{{ asset('sun.svg') }}" alt="Sun" class="greeting-icon">
            <h5 class="card-title">Buenos días {{ $username }}</h5>
        @elseif ($timeOfDay === 'afternoon')
            <img src="{{ asset('sun.svg') }}" alt="Sun" class="greeting-icon">
            <h5 class="card-title">Buenas tardes {{ $username }}</h5>
        @else
            <img src="{{ asset('moon.svg') }}" alt="Moon" class="greeting-icon">
            <h5 class="card-title">Buenas noches {{ $username }}</h5>
        @endif
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

<div data-tor-slider="indicators(true)">
    <div class="h-15rem bg-primary"></div>
    <div class="h-15rem bg-purple"></div>
    <div class="h-15rem bg-navy"></div>
    <div class="h-15rem bg-maroon"></div>
</div>


<div class="row">
    <div class="col-lg-3 col-6 ">
        <!-- Small Box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>Ordenes</h3>
                <p>New Orders</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <!-- Small Box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>Ordenes</h3>
                <p>New Orders</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- Agrega más SmallBox aquí si es necesario -->
</div>



<div class="card text-center">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" aria-current="true" href="#">Active</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <h5 class="card-title">Special title treatment</h5>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="btn btn-primary">Go somewhere</a>
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