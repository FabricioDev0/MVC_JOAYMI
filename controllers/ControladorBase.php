<?php

require_once __DIR__ . '/../config/Sistema.php';

/**
 * Controlador base para todos los controladores del sistema
 */
abstract class ControladorBase
{
    protected $respuestaJson;
    protected $codigoUsuarioSesion;

    public function __construct()
    {
        // Suprimir errores para APIs
        error_reporting(0);
        ini_set('display_errors', 0);
        
        Sistema::iniciarSesion();
        $this->codigoUsuarioSesion = Sistema::obtenerUsuarioSesion();
        $this->respuestaJson = [
            'exito' => false,
            'mensaje' => '',
            'datos' => null
        ];
    }

    /**
     * Verifica si el usuario está autenticado
     */
    protected function verificarAutenticacion()
    {
        return Sistema::usuarioAutenticado();
    }

    /**
     * Verifica si el usuario es administrador
     */
    protected function verificarEsAdministrador()
    {
        return Sistema::esAdministrador();
    }

    /**
     * Envía respuesta JSON al cliente y termina ejecución
     */
    protected function enviarRespuestaJson($respuesta = null)
    {
        // Limpiar cualquier output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        if ($respuesta === null) {
            $respuesta = Sistema::generarRespuesta(
                $this->respuestaJson['exito'],
                $this->respuestaJson['mensaje'],
                $this->respuestaJson['datos']
            );
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Valida que los campos requeridos estén presentes
     */
    protected function validarCamposRequeridos($camposRequeridos, $datos)
    {
        $validacion = Sistema::validarCamposRequeridos($camposRequeridos, $datos);
        
        if (!$validacion['valido']) {
            $this->respuestaJson['mensaje'] = $validacion['mensaje'];
            return false;
        }
        
        return true;
    }

    /**
     * Obtiene los datos de la petición según el método HTTP
     */
    protected function obtenerDatosPeticion()
    {
        $metodoHttp = $_SERVER['REQUEST_METHOD'];
        
        switch ($metodoHttp) {
            case 'GET':
                return $_GET;
            case 'POST':
                // Intentar JSON primero, luego POST
                $datosJson = json_decode(file_get_contents('php://input'), true);
                return $datosJson ?: $_POST;
            case 'PUT':
            case 'DELETE':
                $datosJson = file_get_contents('php://input');
                return json_decode($datosJson, true) ?? [];
            default:
                return [];
        }
    }

    /**
     * Verifica permisos antes de ejecutar acciones
     */
    protected function verificarPermisos($requiereAdmin = false)
    {
        if (!$this->verificarAutenticacion()) {
            $this->respuestaJson['mensaje'] = 'Acceso no autorizado - Debe iniciar sesión';
            return false;
        }

        if ($requiereAdmin && !$this->verificarEsAdministrador()) {
            $this->respuestaJson['mensaje'] = 'Acceso denegado - Requiere permisos de administrador';
            return false;
        }

        return true;
    }

    /**
     * Maneja las peticiones según el método HTTP
     */
    public function manejarPeticion()
    {
        try {
            $metodoHttp = $_SERVER['REQUEST_METHOD'];
            $accion = $_GET['accion'] ?? 'listar';

            switch ($metodoHttp) {
                case 'GET':
                    $this->manejarGet($accion);
                    break;
                case 'POST':
                    $this->manejarPost($accion);
                    break;
                case 'PUT':
                    $this->manejarPut($accion);
                    break;
                case 'DELETE':
                    $this->manejarDelete($accion);
                    break;
                default:
                    $this->respuestaJson['mensaje'] = 'Método HTTP no soportado';
                    $this->enviarRespuestaJson();
            }
        } catch (Exception $excepcion) {
            $this->respuestaJson['mensaje'] = 'Error interno del servidor';
            $this->enviarRespuestaJson();
        }
    }

    /**
     * Muestra la vista de seguridad cuando se detecta intento de acceso al código
     */
    public function seguridad()
    {
        if (!$this->verificarAutenticacion()) {
            header('Location: /Login');
            exit();
        }
        
        $this->render('Main/Seguridad');
    }

    /**
     * Renderiza una vista
     */
    protected function render($vista, $datos = [])
    {
        $rutaVista = __DIR__ . '/../views/' . $vista . '.php';
        if (file_exists($rutaVista)) {
            extract($datos);
            require $rutaVista;
        } else {
            echo "Vista no encontrada: " . htmlspecialchars($vista);
        }
    }

    // Métodos abstractos
    abstract protected function manejarGet($accion);
    abstract protected function manejarPost($accion);
    abstract protected function manejarPut($accion);
    abstract protected function manejarDelete($accion);
}
