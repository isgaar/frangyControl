@extends('layouts.app')

@section('content')
<!DOCTYPE html>

<head>
    <meta charset="utf-8">

    <title>Frangy Control</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
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
        width: 500%;
        /* La ventana ahora ocupa el 100% del ancho disponible */
        max-width: 400px;
        padding: 40px;
        margin: 0 auto;
        /* Centramos la ventana horizontalmente */
        border: 1px solid white;
        border-radius: 20px;
        backdrop-filter: blur(10px) brightness(30%);
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
</head>

<body>
    <div class="navbar-nav ms-auto">


        <div class="flex justify-center mt-16 px-0 sm:items-center sm:justify-between">


            <div class="ml-4 text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0">
                Control "Frangy" con técnologia Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP
                v{{ PHP_VERSION }})
            </div>
        </div>
    </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');
        let currentIndex = 0;
        const backgrounds = [
            '{{ asset("fondos/image1.jpg") }}',
            '{{ asset("fondos/image2.jpg") }}',
            '{{ asset("fondos/image3.jpg") }}',
            '{{ asset("fondos/image4.jpg") }}',
        ];

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

        setInterval(changeBackground, 100); // Cambiar cada 7 segundos (7000 ms)
    });
    </script>
    @endsection