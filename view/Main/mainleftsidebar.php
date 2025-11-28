<?php
// Obtener información de sesión para mostrar opciones según permisos
$esAdministrador = $_SESSION['es_admin'] ?? false;
$paginaActual = basename($_SERVER['PHP_SELF']);
$directorioActual = basename(dirname($_SERVER['PHP_SELF']));
?>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menú Principal</li>
                
                <!-- Dashboard -->
                <li class="<?php echo ($directorioActual == 'Inicio') ? 'mm-active' : ''; ?>">
                    <a href="../Inicio/" class="waves-effect">
                        <div class="d-inline-block icons-sm me-1">
                            <i class="mdi mdi-view-dashboard"></i>
                        </div>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Gestión de Inventario -->
                <li class="menu-title">Gestión de Inventario</li>
                
                <!-- Productos -->
                <li class="<?php echo ($directorioActual == 'Productos') ? 'mm-active' : ''; ?>">
                    <a href="../Productos/" class="waves-effect">
                        <div class="d-inline-block icons-sm me-1">
                            <i class="mdi mdi-package-variant"></i>
                        </div>
                        <span>Productos</span>
                    </a>
                </li>

                <!-- Categorías -->
                <li class="<?php echo ($directorioActual == 'Categorias') ? 'mm-active' : ''; ?>">
                    <a href="../Categorias/" class="waves-effect">
                        <div class="d-inline-block icons-sm me-1">
                            <i class="mdi mdi-folder-multiple"></i>
                        </div>
                        <span>Categorías</span>
                    </a>
                </li>

                <!-- Proveedores -->
                <li class="<?php echo ($directorioActual == 'Proveedores') ? 'mm-active' : ''; ?>">
                    <a href="../Proveedores/" class="waves-effect">
                        <div class="d-inline-block icons-sm me-1">
                            <i class="mdi mdi-domain"></i>
                        </div>
                        <span>Proveedores</span>
                    </a>
                </li>

                <!-- Administración (Solo para administradores) -->
                <?php if ($esAdministrador): ?>
                <li class="menu-title">Administración</li>
                
                <!-- Usuarios -->
                <li class="<?php echo ($directorioActual == 'Usuarios') ? 'mm-active' : ''; ?>">
                    <a href="../Usuarios/" class="waves-effect">
                        <div class="d-inline-block icons-sm me-1">
                            <i class="mdi mdi-account-group"></i>
                        </div>
                        <span>Usuarios</span>
                    </a>
                </li>
                <!-- Roles -->
                <!-- 
                <li class="<?php echo ($directorioActual == 'Roles') ? 'mm-active' : ''; ?>">
                    <a href="../Roles/" class="waves-effect">
                        <div class="d-inline-block icons-sm me-1">
                            <i class="mdi mdi-shield-account"></i>
                        </div>
                        <span>Roles</span>
                    </a>
                </li>
                -->
                <?php endif; ?>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
