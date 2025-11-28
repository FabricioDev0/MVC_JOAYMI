<?php
/**
 * API REST para gestión de usuarios del sistema JOAYMI
 * Maneja CRUD completo de usuarios con control de permisos
 */

// CORS para permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir controlador de usuarios
require_once '../controllers/ControladorUsuario.php';

try {
    $controlador = new ControladorUsuario();
    $metodo = $_SERVER["REQUEST_METHOD"];
    $accion = $_GET['accion'] ?? 'listar';

    // Mapear acciones según el método HTTP para mayor claridad
    switch ($metodo) {
        case 'GET':
            $accion = $_GET['accion'] ?? 'listar';
            break;
        case 'POST':
            $accion = $_GET['accion'] ?? 'crear';
            break;
        case 'PUT':
            $accion = $_GET['accion'] ?? 'actualizar';
            break;
        case 'DELETE':
            $accion = $_GET['accion'] ?? 'eliminar';
            break;
    }

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
