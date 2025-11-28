<?php
/**
 * API REST para autenticación del sistema JOAYMI
 */

// Suprimir errores de PHP para que no interfieran con JSON
error_reporting(0);
ini_set('display_errors', 0);

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Función para enviar respuesta JSON limpia
function enviarRespuestaError($mensaje, $codigo = 500) {
    http_response_code($codigo);
    echo json_encode([
        'exito' => false,
        'mensaje' => $mensaje,
        'datos' => null,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

try {
    // Verificar que el archivo del controlador existe
    $rutaControlador = __DIR__ . '/../controllers/ControladorAuth.php';
    if (!file_exists($rutaControlador)) {
        enviarRespuestaError('Controlador de autenticación no encontrado', 500);
    }
    
    // Incluir controlador
    require_once $rutaControlador;
    
    // Verificar que la clase existe
    if (!class_exists('ControladorAuth')) {
        enviarRespuestaError('Clase ControladorAuth no encontrada', 500);
    }
    
    $controlador = new ControladorAuth();
    $accion = $_GET['accion'] ?? 'verificar';
    
    $_GET['accion'] = $accion;
    $controlador->manejarPeticion();
    
} catch (Exception $e) {
    enviarRespuestaError('Error interno del servidor: ' . $e->getMessage(), 500);
} catch (Error $e) {
    enviarRespuestaError('Error fatal del servidor: ' . $e->getMessage(), 500);
}
?>
