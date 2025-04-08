// Inicializar plugins de AdminLTE
document.addEventListener('DOMContentLoaded', function() {
    // Activar tooltips (opcional)
    $('[data-toggle="tooltip"]').tooltip();
    
    // Inicializar dropdowns
    $('.dropdown-toggle').dropdown();
    
    // Ejemplo: Actualizar notificaciones
    setInterval(() => {
        const badge = $('.navbar-badge');
        const count = parseInt(badge.text()) || 0;
        badge.text(count + 1).fadeIn(200).fadeOut(200).fadeIn(200);
    }, 10000); // Simula nuevas notificaciones cada 10 segundos
});