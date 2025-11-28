<?php
// Iniciar sesión para obtener datos del usuario
require_once __DIR__ . '/../../config/Sistema.php';
Sistema::iniciarSesion();

$nombreUsuario = $_SESSION['str_correo'] ?? 'Usuario';
$esAdministrador = $_SESSION['es_admin'] ?? false;
?>

<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="../Inicio/" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="../../public/images/logo-sm-dark.png" alt="JOAYMI" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="../../public/images/logo-dark.png" alt="JOAYMI" height="20">
                    </span>
                </a>

                <a href="../Inicio/" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="../../public/images/logo-sm-light.png" alt="JOAYMI" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="../../public/images/logo-light.png" alt="JOAYMI" height="20">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                <i class="mdi mdi-backburger"></i>
            </button>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown" 
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0" 
                     aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="form-group mt-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Buscar..." aria-label="Buscar">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Perfil de usuario -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item user text-start d-flex align-items-center" 
                        id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="../../public/images/users/avatar-1.jpg" 
                         alt="Avatar de <?php echo htmlspecialchars($nombreUsuario); ?>">
                    <span class="d-none d-sm-inline-block ms-1">
                        <?php echo htmlspecialchars(explode('@', $nombreUsuario)[0]); ?>
                    </span>
                    <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <?php if ($esAdministrador): ?>
                    <a class="dropdown-item" href="../Usuarios/">
                        <i class="mdi mdi-account-settings font-size-16 align-middle me-1"></i> Gestión de Usuarios
                    </a>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="cerrarSesionUsuario()">
                        <i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Funciones del header
function verPerfilUsuario() {
    // TODO: Implementar modal de perfil
    console.log('Ver perfil de usuario');
}

function cambiarContrasena() {
    // TODO: Implementar modal de cambio de contraseña
    console.log('Cambiar contraseña');
}

function cerrarSesionUsuario() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        text: '¿Estás seguro de que deseas cerrar tu sesión?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Llamar al API de logout
            fetch('../../public/auth_api.php?accion=logout', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.exito) {
                    window.location.href = '../Login/';
                } else {
                    Swal.fire('Error', 'No se pudo cerrar la sesión', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Redirigir al login de todas formas
                window.location.href = '../Login/';
            });
        }
    });
}
</script>
