<?php
/**
 * API REST para consulta de roles del sistema JOAYMI
 * Solo permite consultas - Los roles son predefinidos
 */

// CORS para permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Solo permitir GET para roles
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Método no permitido - Solo se permiten consultas GET',
        'datos' => null,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

// Incluir controlador de roles
require_once '../controllers/ControladorRol.php';

try {
    $controlador = new ControladorRol();
    $accion = $_GET['accion'] ?? 'listar';
    
    $_GET['accion'] = $accion;
    $controlador->manejarPeticion();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error interno del servidor',
        'datos' => null,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
