<?php

require_once __DIR__ . '/ControladorBase.php';
require_once __DIR__ . '/../models/Rol.php';

/**
 * Controlador para gestión de roles
 * Solo permite consultas - Los roles son predefinidos en el sistema
 */
class ControladorRol extends ControladorBase
{
    private $modeloRol;

    public function __construct()
    {
        parent::__construct();
        $this->modeloRol = new Rol();
    }

    protected function manejarGet($accion)
    {
        if (!$this->verificarPermisos()) {
            $this->enviarRespuestaJson();
        }

        switch ($accion) {
            case 'listar':
                $this->listarRoles();
                break;
            case 'buscar':
                $this->buscarRol();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción GET no válida';
                $this->enviarRespuestaJson();
        }
    }

    protected function manejarPost($accion)
    {
        $this->respuestaJson['mensaje'] = 'No se permite crear roles - Son predefinidos del sistema';
        $this->enviarRespuestaJson();
    }

    protected function manejarPut($accion)
    {
        $this->respuestaJson['mensaje'] = 'No se permite modificar roles - Son predefinidos del sistema';
        $this->enviarRespuestaJson();
    }

    protected function manejarDelete($accion)
    {
        $this->respuestaJson['mensaje'] = 'No se permite eliminar roles - Son predefinidos del sistema';
        $this->enviarRespuestaJson();
    }

    private function listarRoles()
    {
        $roles = $this->modeloRol->listarRoles();
        
        $this->respuestaJson['exito'] = true;
        $this->respuestaJson['datos'] = $roles;
        $this->respuestaJson['mensaje'] = 'Roles obtenidos correctamente';
        
        $this->enviarRespuestaJson();
    }

    private function buscarRol()
    {
        $codigoRol = $_GET['codigo'] ?? null;
        
        if (!$codigoRol) {
            $this->respuestaJson['mensaje'] = 'Código de rol requerido';
            $this->enviarRespuestaJson();
        }

        $rol = $this->modeloRol->buscarRolPorCodigo($codigoRol);
        
        if ($rol) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['datos'] = $rol;
            $this->respuestaJson['mensaje'] = 'Rol encontrado';
        } else {
            $this->respuestaJson['mensaje'] = 'Rol no encontrado';
        }
        
        $this->enviarRespuestaJson();
    }
}
