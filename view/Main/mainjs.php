<!-- jquery js -->
<script src="../../public/libs/jquery/jquery.min.js"></script>

<!-- Bootstrap Bundle js -->
<script src="../../public/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- MetisMenu js -->
<script src="../../public/libs/metismenu/metisMenu.min.js"></script>

<!-- Simplebar js -->
<script src="../../public/libs/simplebar/simplebar.min.js"></script>

<!-- Wave js -->
<script src="../../public/libs/node-waves/waves.min.js"></script>

<!-- Bundle js -->
<script src="https://unicons.iconscout.com/release/v2.0.1/script/monochrome/bundle.js"></script>

<!-- App js -->
<script src="../../public/js/app.js"></script>

<!-- Datatables JS -->
<script src="../../public/libs/datatables/jquery.dataTables.js"></script>
<script src="../../public/libs/datatables-responsive/dataTables.responsive.js"></script>
<script src="../../public/datatables/dataTables.buttons.min.js"></script>
<script src="../../public/datatables/buttons.html5.min.js"></script>
<script src="../../public/datatables/buttons.colVis.min.js"></script>
<script src="../../public/datatables/jszip.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<!-- Scripts globales del sistema JOAYMI -->
<script>
// Configuración global de AJAX para CSRF y headers
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        // Agregar headers comunes
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    },
    error: function(xhr, status, error) {
        // Manejo global de errores
        if (xhr.status === 401) {
            Swal.fire({
                title: 'Sesión Expirada',
                text: 'Tu sesión ha expirado. Serás redirigido al login.',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.href = '../Login/';
            });
        }
    }
});

// Función global para formatear fechas
function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    const fechaObj = new Date(fecha);
    return fechaObj.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Función global para formatear precios
function formatearPrecio(precio) {
    if (!precio) return '$0.00';
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'USD'
    }).format(precio);
}

// Función global para mostrar mensajes de éxito
function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: mensaje,
        timer: 3000,
        showConfirmButton: false
    });
}

// Función global para mostrar mensajes de error
function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    });
}

// Función global para confirmar eliminaciones
function confirmarEliminacion(mensaje = '¿Estás seguro de eliminar este elemento?') {
    return Swal.fire({
        title: '¿Confirmar eliminación?',
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
}
</script>
