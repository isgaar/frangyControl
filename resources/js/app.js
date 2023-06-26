import './bootstrap';

$(document).ready(function() {
    setTimeout(function() {
        $('#flash-notification').fadeOut('slow');
    }, 5000); // Duración del toast en milisegundos
});