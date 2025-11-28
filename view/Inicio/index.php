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
    <title>Inicio | Sistema JOAYMI</title>
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
                                <h4 class="page-title mb-1">Dashboard</h4>
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item active">Bienvenido al Sistema JOAYMI</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <!-- Nuevo contenido del wrapper -->
                <div class="page-content-wrapper">
                    <div class="container-fluid">
                        <!-- Bienvenida y Resumen del Sistema -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h5>¡Bienvenido de vuelta!</h5>
                                            <p class="text-muted mb-0">Sistema JOAYMI</p>
                                        </div>
                                        <div class="flex-shrink-0 ms-3">
                                            <img src="../../public/images/widget-img.png" alt="Dashboard" class="img-fluid" style="max-height: 60px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="header-title mb-3">Resumen del Sistema</h5>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-1">Total Productos</p>
                                                <h4 id="totalProductos">0</h4>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <input data-plugin="knob" data-width="56" data-height="56" data-linecap="round" data-displayInput="false"
                                                    data-fgColor="#3051d3" value="75" data-skin="tron" data-angleOffset="56"
                                                    data-readOnly="true" data-thickness=".17" />
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-1">Estado del Inventario</p>
                                                <h5 class="mb-0" id="estadoInventario">Cargando... <span class="text-muted ms-1 font-size-14">productos activos</span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Estadísticas del Sistema -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="header-title mb-4">Estadísticas del Sistema</h5>
                                        <div class="row text-center">
                                            <div class="col-6 col-md-3">
                                                <h4 class="text-primary" id="totalUsuarios">0</h4>
                                                <p class="text-muted mb-0">Usuarios</p>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <h4 class="text-success" id="totalCategorias">0</h4>
                                                <p class="text-muted mb-0">Categorías</p>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <h4 class="text-warning" id="totalProveedores">0</h4>
                                                <p class="text-muted mb-0">Proveedores</p>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <h4 class="text-info" id="totalProductosActivos">0</h4>
                                                <p class="text-muted mb-0">Productos Activos</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Productos Recientes y Categorías Principales -->
                        <div class="row d-flex align-items-stretch">
                            <div class="col-lg-8">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="header-title mb-4">Productos Recientes</h5>
                                        <div class="scrollable-table-container">
                                            <div class="table-responsive">
                                                <table class="table table-centered table-hover mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Código</th>
                                                            <th scope="col">Producto</th>
                                                            <th scope="col" class="d-none d-md-table-cell">Categoría</th>
                                                            <th scope="col">Stock</th>
                                                            <th scope="col">Precio</th>
                                                            <th scope="col" class="d-none d-sm-table-cell">Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tablaProductosRecientes">
                                                        <tr>
                                                            <td colspan="6" class="text-center">
                                                                <div class="spinner-border text-primary" role="status">
                                                                    <span class="visually-hidden">Cargando...</span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="header-title mb-3">Categorías Principales</h5>
                                        <div class="scrollable-table-container">
                                            <div class="table-responsive">
                                                <table class="table table-centered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Categoría</th>
                                                            <th>Descripción</th>
                                                            <th>Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tablaCategoriasPrincipales">
                                                        <tr>
                                                            <td colspan="3" class="text-center">
                                                                <div class="spinner-border text-primary" role="status">
                                                                    <span class="visually-hidden">Cargando...</span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- container-fluid -->
                </div>
                <!-- end page-content-wrapper -->
                <!-- ========== Footer Start ========== -->
                <?php require_once("../Main/mainfooter.php"); ?>
                <!-- ========== Footer FIN ========== -->
            </div>
            <!-- End Page-content -->
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    <!-- JAVASCRIPT -->
    <?php require_once("../Main/mainjs.php"); ?>
    <!-- jQuery Knob para gráficos circulares -->
    <script src="../../public/libs/jquery-knob/jquery.knob.min.js"></script>
    <!-- Inicio JS -->
    <script src="./inicio.js"></script>
</body>
</html>