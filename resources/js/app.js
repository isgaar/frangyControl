import './bootstrap';

$(document).ready(function() {
    setTimeout(function() {
        $('#flash-notification').fadeOut('slow');
    }, 5000); // Duración del toast en milisegundos
});

$(document).ready(function() {
    $('.datepicker').datepicker({
        minDate: new Date() // Oculta días anteriores al día actual
    });

    // Obtener el horario actual en la Ciudad de México
    var now = new Date();
    var currentHour = now.getHours();
    var currentMinute = now.getMinutes();

    // Configurar el horario mínimo para hoy en la Ciudad de México
    if (currentHour >= 19 || (currentHour === 18 && currentMinute >= 30)) {
        // Si es igual o después de las 18:30, ocultar el día de hoy también
        $('.datepicker').datepicker('setStartDate', '+2d');
    } else {
        // Si es antes de las 18:30, ocultar solo el día de ayer
        $('.datepicker').datepicker('setStartDate', '+1d');
    }
});
