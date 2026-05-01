<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ Route::has('landing.pages.welcome') ? route('landing.pages.welcome') : url('/') }}">
            Frangy Control
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ Route::has('landing.pages.welcome') ? route('landing.pages.welcome') : url('/') }}">Inicio</a>
                </li>
                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="btn btn-primary ms-lg-2" href="{{ route('login') }}">Inicia sesión</a>
                    </li>
                @endif
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary ms-lg-2 mt-2 mt-lg-0" href="{{ route('register') }}">Regístrate</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
