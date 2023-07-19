@extends('layouts.app')

@section('content')
<style>
body {
    height: 100vh;
    background: url('{{ asset("fondos/image1.jpg") }}'),
    url('{{ asset("fondos/image2.jpg") }}'),
    url('{{ asset("fondos/image3.jpg") }}'),
    url('{{ asset("fondos/image4.jpg") }}');
    background-size: cover;
    background-position: center;
    transition: opacity 0.5s ease;
    /* Agregamos la transición de opacidad */
    font-family: 'Inter', sans-serif;
    /* Agregamos la fuente de letra */
}

.container {
    text-align: center;
}

.window {
    width: 100%;
    /* La ventana ahora ocupa el 100% del ancho disponible */
    max-width: 500px;
    padding: 40px;
    margin: 0 auto;
    /* Centramos la ventana horizontalmente */
    border-radius: 15px;
    backdrop-filter: blur(15px) brightness(25%);
    color: white;
}

/* Estilos adicionales para que el formulario sea más responsivo */
input[type="email"],
input[type="password"] {
    border: 1px solid white;
    color: white !important;
    background: transparent;
}

/* Estilo para el botón del ojo */
#togglePassword {
    border: 1px solid white;
    background-color: transparent;
    margin-left: -1px;
    /* Ajustamos el margen izquierdo para que quede pegado al campo de contraseña */
}

form {
    width: 100%;
}

input[type="email"]:focus,
input[type="password"]:focus {
    background: #00000040;
    box-shadow: none;
    border: 1px solid white;
}

input[type="email"]::placeholder,
input[type="password"]::placeholder {
    color: white;
}

input[type="submit"] {
    width: 100%;
    max-width: 300px;
    margin-top: 20px;
    border-radius: 8px;
    text-transform: uppercase;
    font-weight: bold;
    font-size: 14px;
}
</style>

<head>
    <!-- Otras etiquetas en el head -->

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="window">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <h2>{{ __('Acceder') }}</h2>

                        <label for="email">{{ __('Correo electrónico') }}</label>
                        <input type="email" class="mt-4 form-control" name="email" value="{{ old('email') }}" required
                            autocomplete="email" autofocus>
                        <span class="error-message" id="email-error"></span>


                        <div class="row mb-3">
                            <label for="password">{{ __('Contraseña') }}</label>
                            <div class="input-group">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    required autocomplete="current-password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye" id="eye-icon"></i>
                                    </button>
                                </div>
                            </div>
                            <span class="error-message" id="password-error"></span>
                        </div>





                        <div class="row mb-3">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Recuérdame') }}
                                </label>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <button type="submit" class="btn btn-info">
                                {{ __('Acceder') }}
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
@if($errors -> has('login_error'))
Swal.fire({
    icon: 'error',
    title: 'Error de inicio de sesión',
    text: '{{ $errors->first('
    login_error ') }}',
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 6000, // Color de fondo traslúcido}
});
@endif
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const togglePasswordButton = document.getElementById('togglePassword');
    let currentIndex = 0;
    const emailInput = document.querySelector('input[name="email"]');
    const emailError = document.getElementById('email-error');
    const backgrounds = [
        '{{ asset("fondos/image1.jpg") }}',
        '{{ asset("fondos/image2.jpg") }}',
        '{{ asset("fondos/image3.jpg") }}',
        '{{ asset("fondos/image4.jpg") }}',
    ];

    emailInput.addEventListener('input', function(event) {
        const emailValue = event.target.value;
        const invalidCharacters = emailValue.replace(/[a-z@.]/g,
        ''); // Remover letras minúsculas, @ y .

        if (invalidCharacters.length > 0) {
            event.target.value = emailValue.replace(/[^\w@.]/g,
            ''); // Eliminar los caracteres inválidos
        }
    });

    togglePasswordButton.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Cambiar el ícono del botón según el tipo de entrada
        if (type === 'text') {
            togglePasswordButton.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            togglePasswordButton.innerHTML = '<i class="fas fa-eye"></i>';
        }
    });

    function changeBackground() {
        currentIndex = (currentIndex + 1) % backgrounds.length;
        document.body.style.opacity = 0; // Establecemos la opacidad a 0 para el desvanecimiento

        // Cambiamos la imagen después de un corto retraso para que se note el desvanecimiento
        setTimeout(() => {
            document.body.style.backgroundImage = `url('${backgrounds[currentIndex]}')`;
        }, 400); // Retraso de 400 ms para que termine el desvanecimiento

        // Establecemos la opacidad nuevamente a 1 después de otro retraso
        setTimeout(() => {
            document.body.style.opacity = 1;
        }, 500); // Retraso de 500 ms para que aparezca la nueva imagen con el desvanecimiento
    }

    setInterval(changeBackground, 70000); // Cambiar cada 7 segundos (7000 ms)
});
</script>
@endsection