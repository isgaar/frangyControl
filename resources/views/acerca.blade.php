@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
<style>
.logo-utcv {
    max-width: 100px;
    height: auto;
}

.center-text {
    position: relative;
    display: flex;
    justify-content: center;
    /* Agregamos esto para centrar verticalmente */
    height: flex;
    z-index: 2;
    /* Modificamos la altura para ocupar toda la pantalla */

}

.text-animation {
    animation: scroll-up 40s linear infinite;
    text-align: center;
    /* Centramos horizontalmente el contenido */
    font-family: monospace;
    /* Usamos una fuente monoespaciada para mantener el formato */
    font-size: 16px;
    /* Ajustamos el tamaño de la fuente para que quepa en la pantalla */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;/
}

@keyframes scroll-up {
    0% {
        transform: translateY(100%);
    }

    100% {
        transform: translateY(-100%);
    }
}

/* Estilos para el footer */
.footer {
    position: absolute;
    bottom: 0;
    background-color: #f8f9fa;
    padding: flex;
    display: flex;
    justify-content: space-between;
    z-index: 2;
}

.footer a {
    color: #000;
    margin-right: 15px;
    font-size: 20px;
}

.footer a:hover {
    color: #007bff; /* Cambiamos el color al pasar el mouse por encima */
}
</style>
<?php
$user_agent = $_SERVER['HTTP_USER_AGENT'];

if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident/') !== false) {
    $browser = 'Internet Explorer';
} elseif (strpos($user_agent, 'Edg') !== false) {
    $browser = 'Microsoft Edge';
} elseif (strpos($user_agent, 'Firefox') !== false) {
    $browser = 'Mozilla Firefox';
} elseif (strpos($user_agent, 'Chrome') !== false) {
    $browser = 'Google Chrome o basados en Chromium';
} elseif (strpos($user_agent, 'Safari') !== false) {
    $browser = 'Apple Safari';
} elseif (strpos($user_agent, 'Opera') !== false || strpos($user_agent, 'OPR/') !== false) {
    $browser = 'Opera';
} else {
    $browser = 'Otro Navegador';
}

?>

<div class="center-text">
    <div class="card">
        <div class="row no-gutters">
            <div class="col-6">
                <img src="http://www.utcv.edu.mx/images/logos/logo_web_sm.png" class="card-img logo-utcv"
                    alt="Logo UTCV">
            </div>
            <div class="col-6">
                <div class="card-body">
                    <p class="card-text">
                        Proyecto de estadía para obtener el grado académico de T.S.U. en Desarrollo de Software
                        Multiplataforma
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="center-text text-animation">
    <pre>
⠀⠀⠀⠀⠀   ⣠⡖⠒⢤⣠⡤⠀⢒⣲⣬⣭⣭⣭⣶⣤⣤⡤⠤⠒⣼⡅⠀
⠀⠀⠀⠀⠀⠀ ⡟⣉⣙⣲⠔⠘⠿⠿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣌⡳⣴⣿⠀
⠀⠀⠀⠀⠀⠀⠀⢳⣘⠟⠀⠀⠀⢠⡾⠛⢉⠛⡿⡟⡳⢬⣭⣴⡌⠻⡇⠀⠀
⠀⠀⠀⠀⠀⠀⠰⢞⣭⠀⠀⠀⠀⢸⣇⠫⠉⢓⡶⠃⢉⢶⣭⡽⢀⠀⢱⠀⠀
⠀⠀⠀⠀⠀⢘⣇⣾⡿⠀⠀⠀⠀⠹⢿⣿⣿⡏⠀⠁⠀⠀⠹⡀⠈⠉⡟⠀⠀
⠀⠀⣠⣶⠞⣽⣿⡕⣡⠀⠀⠀⢀⣠⣭⣿⠇⠀⡴⣶⢾⣶⡽⡄⠀⡇⠀⠀
⠀⠀⣴⣿⣿⠇⣸⣿⣿⠞⣡⠞⡄⠀⠞⠻⠿⢿⣧⣀⢻⡏⣾⡿⠇⣷⣾⡇⠀⠀
⢠⣾⣿⡟⠁⠀⢼⣿⣿⡟⣡⡾⠃⣠⢶⣶⣦⣜⠿⣿⡦⠥⠿⢋⣰⣿⣿⣿⡄⠀
⢸⣿⠏⣠⡀⠀⣿⣿⣿⣿⣿⡤⣐⡁⢻⣿⣿⣿⡿⣷⣶⣿⣿⣿⣿⣿⣿⣿⣿⡄
⢹⣟⣼⣷⣿⡀⣻⣿⣿⣿⣿⣿⡿⢠⣄⡄⡁⠈⠘⢁⢿⣿⣿⣿⣿⣿⣿⣿⣿⠃
⠨⠿⡿⢿⣿⣷⣿⣿⠿⣿⣿⣟⣴⣼⣿⣇⣷⣷⣷⣾⣿⣿⣿⣿⣿⣿⡟⠇⠀
⠀⠀⠀⠈⠹⠉⠀⠀⠀⠘⠙⢻⠟⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠻⣿⠿⡿⠇⠁⠀⠀

- Desarrollado por: Ismael Gaspar Cruz -

Estimados

Por medio de la presente, me complace formalizar el programa dedicado al centro 
de servicio automotriz y llantera "Frangy", el cual ha sido desarrollado como 
parte de mi proyecto para estadía.

Me gustaría expresar mis más sinceros agradecimientos al Lic. Cesar Aldaraca Juárez 
y a la Ing. Rosalina Autrán Velasco por su valioso apoyo constante en la aprobación 
de avances durante mi estadía. También quisiera reconocer que, para resolver problemas 
de código derivado a falta de conocimiento, recurrí a la I.A. Chat GPT, la cual 
brindó una inmediata ayuda.

Además, quiero agradecer infinitamente la paciencia y el constante apoyo de mis padres, 
quienes siempre me han motivado en cada momento de mi vida.

Sin el respaldo y colaboración de todas estas personas, este programa no habría sido 
posible de entregar. 

-- Esta aplicación usa la tecnología --
Versión del proyecto <?php echo config('app.version'); ?>,
PHP <?php echo PHP_VERSION; ?>, 
Laravel <?php echo app()->version(); ?> 
Herramienta: <?php echo $_SERVER['SERVER_SOFTWARE']; ?>, 
Sistema operativo: <?php echo php_uname('s'); ?>.
Cliente web: <?php echo $browser; ?>
    
</pre>
</div>

<div class="footer">
    <!-- Ícono de GitHub -->
    <a href="https://github.com/isgaar/frangyControl/tree/main" target="_blank" rel="noopener noreferrer" title="Repositorio del codigo">
        <i class="fab fa-github"></i>
    </a>
    <!-- Ícono de correo -->
    <a href="mailto:may17jun2002@outlook.com" title="Contacto con el desarrollador">
        <i class="far fa-envelope"></i>
    </a>
</div>

@stop

@section('plugins.FontAwesome', true)
