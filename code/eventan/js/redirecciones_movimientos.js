
document.addEventListener('DOMContentLoaded', function () {
    let idActual = null;

    const modalMenus = document.getElementById('modalMenus');
    modalMenus.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        idActual = button.getAttribute('data-id');
    });

    const links = document.querySelectorAll('.movimiento-link');
    links.forEach(link => {
        link.addEventListener('click', function () {
            const movimiento = this.getAttribute('data-movimiento');
            const baseUrl = this.getAttribute('data-custom-url') || 'showencVentanilla.php';

            if (idActual) {
                const url = `${baseUrl}?id_encVenta=${idActual}&movimiento=${encodeURIComponent(movimiento)}`;
                window.location.href = url;
            }
        });
    });
});

