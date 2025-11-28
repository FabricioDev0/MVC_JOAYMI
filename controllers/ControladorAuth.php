<?php

require_once __DIR__ . '/ControladorBase.php';
require_once __DIR__ . '/../models/Usuario.php';

/**
 * Controlador para manejo de autenticación y sesiones
 * Gestiona login, logout y verificación de permisos
 */
class ControladorAuth extends ControladorBase
{
    private $modeloUsuario;

    public function __construct()
    {
        parent::__construct();
        $this->modeloUsuario = new Usuario();
    }

    /**
     * Maneja peticiones GET para autenticación
     * 
     *  $accion Acción solicitada
     */
    protected function manejarGet($accion)
    {
        switch ($accion) {
            case 'verificar':
                $this->verificarSesion();
                break;
            case 'perfil':
                $this->obtenerPerfil();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción no válida para GET';
                $this->enviarRespuestaJson();
        }
    }

    /**
     * Maneja peticiones POST para autenticación
     * 
     *  $accion Acción solicitada
     */
    protected function manejarPost($accion)
    {
        switch ($accion) {
            case 'login':
                $this->iniciarSesion();
                break;
            case 'logout':
                $this->cerrarSesion();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción no válida para POST';
                $this->enviarRespuestaJson();
        }
    }

    /**
     * No se permiten peticiones PUT en autenticación
     */
    protected function manejarPut($accion)
    {
        $this->respuestaJson['mensaje'] = 'Método PUT no permitido en autenticación';
        $this->enviarRespuestaJson();
    }

    /**
     * No se permiten peticiones DELETE en autenticación
     */
    protected function manejarDelete($accion)
    {
        $this->respuestaJson['mensaje'] = 'Método DELETE no permitido en autenticación';
        $this->enviarRespuestaJson();
    }

    /**
     * Procesa el login del usuario
     */
    private function iniciarSesion()
    {
        // Obtener datos tanto de POST como de JSON
        $datosPost = $_POST;
        $datosJson = json_decode(file_get_contents('php://input'), true);
        
        // Usar JSON si está disponible, sino POST
        $datos = !empty($datosJson) ? $datosJson : $datosPost;
        
        $camposRequeridos = ['correoElectronico', 'claveAcceso'];
        
        if (!$this->validarCamposRequeridos($camposRequeridos, $datos)) {
            $this->enviarRespuestaJson();
        }

        // Validar formato de email
        if (!Sistema::validarEmail($datos['correoElectronico'])) {
            $this->respuestaJson['mensaje'] = 'Formato de correo electrónico inválido';
            $this->enviarRespuestaJson();
        }

        $usuario = $this->modeloUsuario->autenticarUsuario(
            $datos['correoElectronico'],
            $datos['claveAcceso']
        );

        if ($usuario) {
            // Establecer datos de sesión
            $_SESSION['cod_usuario'] = $usuario['cod_usuario'];
            $_SESSION['str_correo'] = $usuario['str_correo'];
            $_SESSION['cod_rol'] = $usuario['cod_rol'];
            $_SESSION['es_admin'] = ($usuario['cod_rol'] == Sistema::ROL_ADMINISTRADOR);
            
            // Remover clave por seguridad
            unset($usuario['str_clave']);
            
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['datos'] = [
                'usuario' => $usuario,
                'permisos' => [
                    'esAdministrador' => $_SESSION['es_admin'],
                    'puedeCrear' => true,
                    'puedeEditar' => true,
                    'puedeEliminar' => $_SESSION['es_admin']
                ]
            ];
            $this->respuestaJson['mensaje'] = 'Sesión iniciada correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Credenciales incorrectas';
        }
        
        $this->enviarRespuestaJson();
    }

    /**
     * Cierra la sesión del usuario
     */
    private function cerrarSesion()
    {
        Sistema::cerrarSesion();
        
        $this->respuestaJson['exito'] = true;
        $this->respuestaJson['mensaje'] = 'Sesión cerrada correctamente';
        
        $this->enviarRespuestaJson();
    }

    /**
     * Verifica el estado de la sesión actual
     */
    private function verificarSesion()
    {
        if ($this->verificarAutenticacion()) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['datos'] = [
                'autenticado' => true,
                'usuario' => $_SESSION['str_correo'],
                'esAdministrador' => $this->verificarEsAdministrador()
            ];
            $this->respuestaJson['mensaje'] = 'Sesión válida';
        } else {
            $this->respuestaJson['mensaje'] = 'Sesión no válida';
        }
        
        $this->enviarRespuestaJson();
    }

    /**
     * Obtiene el perfil del usuario actual
     */
    private function obtenerPerfil()
    {
        if (!$this->verificarPermisos()) {
            $this->enviarRespuestaJson();
        }

        $usuario = $this->modeloUsuario->buscarUsuarioPorCodigo($this->codigoUsuarioSesion);
        
        if ($usuario) {
            // Remover datos sensibles
            unset($usuario['str_clave']);
            
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['datos'] = $usuario;
            $this->respuestaJson['mensaje'] = 'Perfil obtenido correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al obtener el perfil';
        }
        
        $this->enviarRespuestaJson();
    }
}
