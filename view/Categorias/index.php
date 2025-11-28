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
    <title>Categorías | Sistema JOAYMI</title>
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
                                <h4 class="page-title mb-1">Gestión de Categorías</h4>
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="../Inicio/">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Categorías</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <div class="page-content-wrapper">
                    <div class="container-fluid">
                        <!-- Mantenimiento CATEGORIAS -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Lista de Categorías de Productos</h4>
                                        <p class="card-title-desc">
                                            Gestiona las categorías que organizan los productos en el Sistema JOAYMI. 
                                            Las categorías ayudan a clasificar y encontrar productos más fácilmente.
                                        </p>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <button class="btn btn-primary waves-effect waves-light" id="btnNuevaCategoria">
                                                    <i class="mdi mdi-folder-plus me-1"></i> Nueva Categoría
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="float-end">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table id="tablaCategorias" class="table table-bordered table-striped dt-responsive nowrap" style="width: 100%">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nombre</th>
                                                        <th>Descripción</th>
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
    
    <!-- Modal de mantenimiento -->
    <?php require_once("modalmantenimiento.php"); ?>
    
    <!-- JAVASCRIPT -->
    <?php require_once("../Main/mainjs.php"); ?>

    <!-- Categorías JS -->
    <script src="./categorias.js"></script>
</body>
</html>
