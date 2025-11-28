<?php
// Verificar autenticación y permisos de administrador
require_once __DIR__ . '/../../config/Sistema.php';
Sistema::iniciarSesion();

// Verificar que el usuario esté autenticado
if (!Sistema::usuarioAutenticado()) {
    header('Location: ../Login/');
    exit();
}

// Verificar que sea administrador
if (!Sistema::esAdministrador()) {
    header('Location: ../Inicio/');
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
    <?php require_once("../Main/mainhead.php"); ?>
    <title>Roles | Sistema JOAYMI</title>
</head>
<body data-topbar="colored">
    <!-- Begin page -->
    <div id="layout-wrapper">
        <!-- ========== Header Start ========== -->
        <?php require_once("../Main/mainheader.php"); ?>
        <!-- ========== Header FIN ========== -->

        <!-- ========== Left Sidebar Start ========== -->
        <?php require_once("../Main/mainleftsidebar.php"); ?>
        <!-- ========== Left Sidebar FIN ========== -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                
                <!-- Page-Title -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="page-title mb-1">Gestión de Roles</h4>
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="../Inicio/">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Roles</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <div class="page-content-wrapper">
                    <div class="container-fluid">

                        <!-- Lista de Roles -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Roles del Sistema JOAYMI</h4>
                                        <p class="card-title-desc">
                                            Consulta los roles predefinidos del sistema y sus características principales.
                                        </p>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="float-end">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table id="tablaRoles" class="table table-bordered table-striped dt-responsive nowrap" style="width: 100%">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nombre del Rol</th>
                                                        <th>Estado</th>
                                                        <th>Fecha Creación</th>
                                                        <th>Descripción</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Los datos se cargan via AJAX -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional sobre roles -->
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="mdi mdi-shield-crown text-warning me-2"></i>
                                            Rol Administrador
                                        </h5>
                                        <p class="card-text">
                                            Los usuarios con rol <strong>Administrador</strong> tienen acceso completo al sistema:
                                        </p>
                                        <ul class="list-unstyled">
                                            <li><i class="mdi mdi-check text-success me-1"></i> Gestión completa de usuarios</li>
                                            <li><i class="mdi mdi-check text-success me-1"></i> CRUD de productos, categorías y proveedores</li>
                                            <li><i class="mdi mdi-check text-success me-1"></i> Acceso a reportes y estadísticas</li>
                                            <li><i class="mdi mdi-check text-success me-1"></i> Configuración del sistema</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="mdi mdi-account text-primary me-2"></i>
                                            Rol Usuario
                                        </h5>
                                        <p class="card-text">
                                            Los usuarios con rol <strong>Usuario</strong> tienen acceso limitado:
                                        </p>
                                        <ul class="list-unstyled">
                                            <li><i class="mdi mdi-check text-success me-1"></i> Consulta de productos y categorías</li>
                                            <li><i class="mdi mdi-check text-success me-1"></i> Creación y edición de registros básicos</li>
                                            <li><i class="mdi mdi-close text-danger me-1"></i> Sin acceso a gestión de usuarios</li>
                                            <li><i class="mdi mdi-close text-danger me-1"></i> Sin permisos de eliminación</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- container-fluid -->
                </div>
                <!-- end page-content-wrapper -->
            </div>
            <!-- End Page-content -->
            
            <!-- ========== Footer Start ========== -->
            <?php require_once("../Main/mainfooter.php"); ?>
            <!-- ========== Footer FIN ========== -->

        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    
    <!-- Right Sidebar -->
    <div class="right-bar">
        <div data-simplebar class="h-100">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#chat-tab" role="tab" aria-selected="true">
                        <span class="d-none d-sm-block"><i class="mdi mdi-message-text font-size-22"></i></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- /Right-bar -->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    
    <!-- Modal de información -->
    <?php require_once("modalmantenimiento.php"); ?>
    
    <!-- JAVASCRIPT -->
    <?php require_once("../Main/mainjs.php"); ?>

    <!-- Roles JS -->
    <script src="./roles.js"></script>
</body>
</html>
