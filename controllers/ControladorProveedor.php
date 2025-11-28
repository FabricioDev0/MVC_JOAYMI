<?php

require_once __DIR__ . '/ControladorBase.php';
require_once __DIR__ . '/../models/Proveedor.php';

/**
 * Controlador para gestión de proveedores
 * Maneja CRUD de proveedores con control de permisos
 */
class ControladorProveedor extends ControladorBase
{
    private $modeloProveedor;

    public function __construct()
    {
        parent::__construct();
        $this->modeloProveedor = new Proveedor();
    }

    protected function manejarGet($accion)
    {
        if (!$this->verificarPermisos()) {
            $this->enviarRespuestaJson();
        }

        switch ($accion) {
            case 'listar':
                $this->listarProveedores();
                break;
            case 'buscar':
                $this->buscarProveedor();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción GET no válida';
                $this->enviarRespuestaJson();
        }
    }

    protected function manejarPost($accion)
    {
        if (!$this->verificarPermisos()) {
            $this->enviarRespuestaJson();
        }

        switch ($accion) {
            case 'crear':
                $this->crearProveedor();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción POST no válida';
                $this->enviarRespuestaJson();
        }
    }

    protected function manejarPut($accion)
    {
        if (!$this->verificarPermisos()) {
            $this->enviarRespuestaJson();
        }

        switch ($accion) {
            case 'actualizar':
                $this->actualizarProveedor();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción PUT no válida';
                $this->enviarRespuestaJson();
        }
    }

    protected function manejarDelete($accion)
    {
        if (!$this->verificarPermisos(true)) {
            $this->enviarRespuestaJson();
        }

        switch ($accion) {
            case 'eliminar':
                $this->eliminarProveedor();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción DELETE no válida';
                $this->enviarRespuestaJson();
        }
    }

    private function listarProveedores()
    {
        $proveedores = $this->modeloProveedor->listarProveedores();
        
        $this->respuestaJson['exito'] = true;
        $this->respuestaJson['datos'] = $proveedores;
        $this->respuestaJson['mensaje'] = 'Proveedores obtenidos correctamente';
        
        $this->enviarRespuestaJson();
    }

    private function buscarProveedor()
    {
        $codigoProveedor = $_GET['codigo'] ?? null;
        
        if (!$codigoProveedor) {
            $this->respuestaJson['mensaje'] = 'Código de proveedor requerido';
            $this->enviarRespuestaJson();
        }

        $proveedor = $this->modeloProveedor->buscarProveedorPorCodigo($codigoProveedor);
        
        if ($proveedor) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['datos'] = $proveedor;
            $this->respuestaJson['mensaje'] = 'Proveedor encontrado';
        } else {
            $this->respuestaJson['mensaje'] = 'Proveedor no encontrado';
        }
        
        $this->enviarRespuestaJson();
    }

    private function crearProveedor()
    {
        $datos = $this->obtenerDatosPeticion();
        $camposRequeridos = ['nombreProveedor', 'contactoProveedor', 'telefonoProveedor'];
        
        if (!$this->validarCamposRequeridos($camposRequeridos, $datos)) {
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloProveedor->insertarProveedor(
            $datos['nombreProveedor'],
            $datos['contactoProveedor'],
            $datos['telefonoProveedor'],
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Proveedor creado correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al crear el proveedor';
        }
        
        $this->enviarRespuestaJson();
    }

    private function actualizarProveedor()
    {
        $datos = $this->obtenerDatosPeticion();
        $camposRequeridos = ['codigoProveedor', 'nombreProveedor', 'contactoProveedor', 'telefonoProveedor'];
        
        if (!$this->validarCamposRequeridos($camposRequeridos, $datos)) {
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloProveedor->actualizarProveedor(
            $datos['codigoProveedor'],
            $datos['nombreProveedor'],
            $datos['contactoProveedor'],
            $datos['telefonoProveedor'],
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Proveedor actualizado correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al actualizar el proveedor';
        }
        
        $this->enviarRespuestaJson();
    }

    private function eliminarProveedor()
    {
        $datos = $this->obtenerDatosPeticion();
        $codigoProveedor = $datos['codigoProveedor'] ?? null;
        
        if (!$codigoProveedor) {
            $this->respuestaJson['mensaje'] = 'Código de proveedor requerido';
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloProveedor->eliminarProveedor(
            $codigoProveedor,
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Proveedor eliminado correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al eliminar el proveedor';
        }
        
        $this->enviarRespuestaJson();
    }
}
