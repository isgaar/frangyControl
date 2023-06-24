<nav class="navbar navbar-expand-lg bg-body-tertiary ">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('landing.index') }}">Dos Zapatos</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="navbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="sidebar-nav-item"><a class="nav-link" href="{{ route('landing.index') }}">Inicio</a></li>
                    <li class="sidebar-nav-item"><a class="nav-link" href="{{ route('landing.catalogo') }}">Catálogo</a>
                    </li>
                    <li class="sidebar-nav-item"><a class="nav-link" href="{{ route('landing.acerca') }}">¿Quiénes
                            somos?</a></li>
                    <li class="sidebar-nav-item"><a class="nav-link" href="{{ route('landing.mision') }}">Misión y
                            Visión</a></li>
                  {{--   <li class="sidebar-nav-item"><a class="nav-link"
                            href="{{ route('landing.contacto') }}">Afíliate</a>
                    </li> --}}
                    <li class="sidebar-nav-item"><a class="nav-link" href="{{ route('login') }}">Inicia sesión</a></li>
                    <li class="sidebar-nav-item"><a class="nav-link" href="{{ route('register') }}">Regístrate</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
