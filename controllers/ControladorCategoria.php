<?php

require_once __DIR__ . '/ControladorBase.php';
require_once __DIR__ . '/../models/Categoria.php';

/**
 * Controlador para gestión de categorías
 * Maneja CRUD de categorías con control de permisos
 */
class ControladorCategoria extends ControladorBase
{
    private $modeloCategoria;

    public function __construct()
    {
        parent::__construct();
        $this->modeloCategoria = new Categoria();
    }

    protected function manejarGet($accion)
    {
        if (!$this->verificarPermisos()) {
            $this->enviarRespuestaJson();
        }

        switch ($accion) {
            case 'listar':
                $this->listarCategorias();
                break;
            case 'buscar':
                $this->buscarCategoria();
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
                $this->crearCategoria();
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
                $this->actualizarCategoria();
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
                $this->eliminarCategoria();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción DELETE no válida';
                $this->enviarRespuestaJson();
        }
    }

    private function listarCategorias()
    {
        $categorias = $this->modeloCategoria->listarCategorias();
        
        $this->respuestaJson['exito'] = true;
        $this->respuestaJson['datos'] = $categorias;
        $this->respuestaJson['mensaje'] = 'Categorías obtenidas correctamente';
        
        $this->enviarRespuestaJson();
    }

    private function buscarCategoria()
    {
        $codigoCategoria = $_GET['codigo'] ?? null;
        
        if (!$codigoCategoria) {
            $this->respuestaJson['mensaje'] = 'Código de categoría requerido';
            $this->enviarRespuestaJson();
        }

        $categoria = $this->modeloCategoria->buscarCategoriaPorCodigo($codigoCategoria);
        
        if ($categoria) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['datos'] = $categoria;
            $this->respuestaJson['mensaje'] = 'Categoría encontrada';
        } else {
            $this->respuestaJson['mensaje'] = 'Categoría no encontrada';
        }
        
        $this->enviarRespuestaJson();
    }

    private function crearCategoria()
    {
        $datos = $this->obtenerDatosPeticion();
        $camposRequeridos = ['nombreCategoria', 'descripcionCategoria'];
        
        if (!$this->validarCamposRequeridos($camposRequeridos, $datos)) {
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloCategoria->insertarCategoria(
            $datos['nombreCategoria'],
            $datos['descripcionCategoria'],
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Categoría creada correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al crear la categoría';
        }
        
        $this->enviarRespuestaJson();
    }

    private function actualizarCategoria()
    {
        $datos = $this->obtenerDatosPeticion();
        $camposRequeridos = ['codigoCategoria', 'nombreCategoria', 'descripcionCategoria'];
        
        if (!$this->validarCamposRequeridos($camposRequeridos, $datos)) {
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloCategoria->actualizarCategoria(
            $datos['codigoCategoria'],
            $datos['nombreCategoria'],
            $datos['descripcionCategoria'],
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Categoría actualizada correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al actualizar la categoría';
        }
        
        $this->enviarRespuestaJson();
    }

    private function eliminarCategoria()
    {
        $datos = $this->obtenerDatosPeticion();
        $codigoCategoria = $datos['codigoCategoria'] ?? null;
        
        if (!$codigoCategoria) {
            $this->respuestaJson['mensaje'] = 'Código de categoría requerido';
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloCategoria->eliminarCategoria(
            $codigoCategoria,
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Categoría eliminada correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al eliminar la categoría';
        }
        
        $this->enviarRespuestaJson();
    }
}
