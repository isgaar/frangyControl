<div class="container px-4 px-lg-5 py-5 text-center">
    <h1 class="display-5 fw-bold mb-3">Frangy Control</h1>
    <p class="lead mb-4">Control operativo para clientes, vehiculos, servicios y ordenes.</p>
    @if (Route::has('login'))
        <a class="btn btn-primary btn-lg" href="{{ route('login') }}">Iniciar sesión</a>
    @endif
</div>
