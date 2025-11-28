<?php
// Verificar autenticación
require_once __DIR__ . '/../../config/Sistema.php';
Sistema::iniciarSesion();

// Verificar que el usuario esté autenticado
if (!Sistema::usuarioAutenticado()) {
    header('Location: ../Login/');
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
    <?php require_once("../Main/mainhead.php"); ?>
    <title>Proveedores | Sistema JOAYMI</title>
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
                                <h4 class="page-title mb-1">Gestión de Proveedores</h4>
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="../Inicio/">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Proveedores</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                
                <div class="page-content-wrapper">
                    <div class="container-fluid">
                        <!-- Mantenimiento PROVEEDORES -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Lista de Proveedores del Sistema</h4>
                                        <p class="card-title-desc">
                                            Gestiona los proveedores que suministran productos al Sistema JOAYMI. 
                                            Mantén actualizada la información de contacto para una mejor comunicación comercial.
                                        </p>
                                    </div>
                                    <div class="card-body">
                                        <button class="btn btn-primary waves-effect waves-light mb-4" id="btnNuevoProveedor">
                                            <i class="mdi mdi-domain-plus me-1"></i> Nuevo Proveedor
                                        </button>
                                        
                                        <div class="table-responsive">
                                            <table id="tablaProveedores" class="table table-bordered table-striped display responsive nowrap" style="width: 100%">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nombre</th>
                                                        <th>Contacto</th>
                                                        <th>Teléfono</th>
                                                        <th>Estado</th>
                                                        <th>Fecha Creación</th>
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
    
    <!-- Modal de mantenimiento -->
    <?php require_once("modalmantenimiento.php"); ?>
    
    <!-- JAVASCRIPT -->
    <?php require_once("../Main/mainjs.php"); ?>

    <!-- DataTables Responsive JS -->
    <script src="../../public/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../public/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

    <!-- Proveedores JS -->
    <script src="./proveedores.js"></script>

    <!-- Toast Notifications -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
        <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
</body>
</html>