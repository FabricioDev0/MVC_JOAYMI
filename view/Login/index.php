<?php
// Verificar autenticación
require_once __DIR__ . '/../../config/Sistema.php';
Sistema::iniciarSesion();

// Si el usuario ya está autenticado, redirigir al dashboard
if (Sistema::usuarioAutenticado()) {
    header('Location: ../Inicio/');
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
    <?php require_once("../Main/mainhead.php"); ?>
    <title>Login | Sistema JOAYMI</title>
</head>
<body class="bg-primary bg-pattern">
    <div class="home-btn d-none d-sm-block">
        <a href="#"><i class="mdi mdi-home-variant h2 text-white"></i></a>
    </div>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mb-5">
                        <a href="#" class="logo"><img src="../../public/images/logo-light.png" height="24" alt="logo"></a>
                        <h5 class="font-size-16 text-white-50 mb-4">Sistema JOAYMI</h5>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div class="row justify-content-center">
                <div class="col-xl-5 col-sm-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="p-2">
                                <h5 class="mb-5 text-center">Inicia sesión para continuar al Sistema JOAYMI.</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-custom mb-4">
                                            <input type="text" class="form-control" id="usuario" required>
                                            <label for="usuario">Usuario</label>
                                        </div>
                                        <div class="form-group form-group-custom mb-4">
                                            <input type="password" class="form-control" id="clave" required>
                                            <label for="clave">Contraseña</label>
                                        </div>
                                        <div class="mt-4">
                                            <button class="btn btn-success d-block w-100 waves-effect waves-light" id="btningresar" type="submit">Iniciar Sesión</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
    </div>
    <!-- end Account pages -->
   
    <?php require_once("../Main/mainjs.php"); ?>
    <script src="./login.js"></script>

    <script>
    // Debug para verificar que todo esté cargado
    console.log("Login page loaded");
    console.log("jQuery loaded:", typeof $ !== 'undefined');
    console.log("SweetAlert loaded:", typeof Swal !== 'undefined');
    </script>

</body>
</html>
