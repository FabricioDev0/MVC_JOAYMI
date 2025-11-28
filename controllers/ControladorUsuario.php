<?php

require_once __DIR__ . '/ControladorBase.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Rol.php';

/**
 * Controlador para gestión de usuarios
 * Maneja CRUD de usuarios con control de permisos
 */
class ControladorUsuario extends ControladorBase
{
    private $modeloUsuario;
    private $modeloRol;

    public function __construct()
    {
        parent::__construct();
        $this->modeloUsuario = new Usuario();
        $this->modeloRol = new Rol();
    }

    protected function manejarGet($accion)
    {
        switch ($accion) {
            case 'listar':
                $this->listarUsuarios();
                break;
            case 'buscar':
                $this->buscarUsuario();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción GET no válida';
                $this->enviarRespuestaJson();
        }
    }

    protected function manejarPost($accion)
    {
        switch ($accion) {
            case 'crear':
                $this->crearUsuario();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción POST no válida';
                $this->enviarRespuestaJson();
        }
    }

    protected function manejarPut($accion)
    {
        switch ($accion) {
            case 'actualizar':
                $this->actualizarUsuario();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción PUT no válida';
                $this->enviarRespuestaJson();
        }
    }

    protected function manejarDelete($accion)
    {
        switch ($accion) {
            case 'eliminar':
                $this->eliminarUsuario();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción DELETE no válida';
                $this->enviarRespuestaJson();
        }
    }

    private function listarUsuarios()
    {
        if (!$this->verificarPermisos(true)) {
            $this->enviarRespuestaJson();
        }

        $usuarios = $this->modeloUsuario->listarUsuarios();
        
        $this->respuestaJson['exito'] = true;
        $this->respuestaJson['datos'] = $usuarios;
        $this->respuestaJson['mensaje'] = 'Usuarios obtenidos correctamente';
        
        $this->enviarRespuestaJson();
    }

    private function buscarUsuario()
    {
        if (!$this->verificarPermisos()) {
            $this->enviarRespuestaJson();
        }

        $codigoUsuario = $_GET['codigo'] ?? null;
        
        if (!$codigoUsuario) {
            $this->respuestaJson['mensaje'] = 'Código de usuario requerido';
            $this->enviarRespuestaJson();
        }

        $usuario = $this->modeloUsuario->buscarUsuarioPorCodigo($codigoUsuario);
        
        if ($usuario) {
            unset($usuario['str_clave']); // Seguridad
            
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['datos'] = $usuario;
            $this->respuestaJson['mensaje'] = 'Usuario encontrado';
        } else {
            $this->respuestaJson['mensaje'] = 'Usuario no encontrado';
        }
        
        $this->enviarRespuestaJson();
    }

    private function crearUsuario()
    {
        if (!$this->verificarPermisos(true)) {
            $this->enviarRespuestaJson();
        }

        $datos = $this->obtenerDatosPeticion();
        $camposRequeridos = ['correoElectronico', 'claveAcceso', 'codigoRol'];
        
        if (!$this->validarCamposRequeridos($camposRequeridos, $datos)) {
            $this->enviarRespuestaJson();
        }

        // Validaciones adicionales
        if (!Sistema::validarEmail($datos['correoElectronico'])) {
            $this->respuestaJson['mensaje'] = 'Formato de correo electrónico inválido';
            $this->enviarRespuestaJson();
        }

        if (strlen($datos['claveAcceso']) < Sistema::CLAVE_MINIMA_LONGITUD) {
            $this->respuestaJson['mensaje'] = 'La contraseña debe tener al menos ' . Sistema::CLAVE_MINIMA_LONGITUD . ' caracteres';
            $this->enviarRespuestaJson();
        }

        // Verificar que el rol existe
        if (!$this->modeloRol->existeRol($datos['codigoRol'])) {
            $this->respuestaJson['mensaje'] = 'El rol especificado no existe';
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloUsuario->insertarUsuario(
            $datos['correoElectronico'],
            $datos['claveAcceso'],
            $datos['codigoRol'],
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Usuario creado correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al crear el usuario - Posible correo duplicado';
        }
        
        $this->enviarRespuestaJson();
    }

    private function actualizarUsuario()
    {
        if (!$this->verificarPermisos()) {
            $this->enviarRespuestaJson();
        }

        $datos = $this->obtenerDatosPeticion();
        $camposRequeridos = ['codigoUsuario', 'correoElectronico', 'codigoRol'];
        
        if (!$this->validarCamposRequeridos($camposRequeridos, $datos)) {
            $this->enviarRespuestaJson();
        }

        // Solo admin puede actualizar otros usuarios o cambiar roles
        $esOtroUsuario = $datos['codigoUsuario'] != $this->codigoUsuarioSesion;
        
        if ($esOtroUsuario && !$this->verificarEsAdministrador()) {
            $this->respuestaJson['mensaje'] = 'No tienes permisos para actualizar otros usuarios';
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloUsuario->actualizarUsuario(
            $datos['codigoUsuario'],
            $datos['correoElectronico'],
            $datos['codigoRol'],
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Usuario actualizado correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al actualizar el usuario';
        }
        
        $this->enviarRespuestaJson();
    }

    private function eliminarUsuario()
    {
        if (!$this->verificarPermisos(true)) {
            $this->enviarRespuestaJson();
        }

        $datos = $this->obtenerDatosPeticion();
        $codigoUsuario = $datos['codigoUsuario'] ?? null;
        
        if (!$codigoUsuario) {
            $this->respuestaJson['mensaje'] = 'Código de usuario requerido';
            $this->enviarRespuestaJson();
        }

        // No permitir auto-eliminación
        if ($codigoUsuario == $this->codigoUsuarioSesion) {
            $this->respuestaJson['mensaje'] = 'No puedes eliminarte a ti mismo';
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloUsuario->eliminarUsuario(
            $codigoUsuario,
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Usuario eliminado correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al eliminar el usuario';
        }
        
        $this->enviarRespuestaJson();
    }
}
