<?php

require_once __DIR__ . '/ControladorBase.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Proveedor.php';

/**
 * Controlador para gestión de productos
 * Maneja CRUD de productos con validaciones y control de permisos
 */
class ControladorProducto extends ControladorBase
{
    private $modeloProducto;
    private $modeloCategoria;
    private $modeloProveedor;

    public function __construct()
    {
        parent::__construct();
        $this->modeloProducto = new Producto();
        $this->modeloCategoria = new Categoria();
        $this->modeloProveedor = new Proveedor();
    }

    protected function manejarGet($accion)
    {
        if (!$this->verificarPermisos()) {
            $this->enviarRespuestaJson();
        }

        switch ($accion) {
            case 'listar':
                $this->listarProductos();
                break;
            case 'buscar':
                $this->buscarProducto();
                break;
            case 'porCategoria':
                $this->buscarProductosPorCategoria();
                break;
            case 'porProveedor':
                $this->buscarProductosPorProveedor();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción GET no vál ida';
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
                $this->crearProducto();
                break;
            case 'actualizarStock':
                $this->actualizarStockProducto();
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
                $this->actualizarProducto();
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
                $this->eliminarProducto();
                break;
            default:
                $this->respuestaJson['mensaje'] = 'Acción DELETE no válida';
                $this->enviarRespuestaJson();
        }
    }

    private function listarProductos()
    {
        $productos = $this->modeloProducto->listarProductos();
        
        $this->respuestaJson['exito'] = true;
        $this->respuestaJson['datos'] = $productos;
        $this->respuestaJson['mensaje'] = 'Productos obtenidos correctamente';
        
        $this->enviarRespuestaJson();
    }

    private function buscarProducto()
    {
        $codigoProducto = $_GET['codigo'] ?? null;
        
        if (!$codigoProducto) {
            $this->respuestaJson['mensaje'] = 'Código de producto requerido';
            $this->enviarRespuestaJson();
        }

        $producto = $this->modeloProducto->buscarProductoPorCodigo($codigoProducto);
        
        if ($producto) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['datos'] = $producto;
            $this->respuestaJson['mensaje'] = 'Producto encontrado';
        } else {
            $this->respuestaJson['exito'] = false;
            $this->respuestaJson['datos'] = null;
            $this->respuestaJson['mensaje'] = 'Producto no encontrado';
        }
        
        $this->enviarRespuestaJson();
    }

    private function buscarProductosPorCategoria()
    {
        $codigoCategoria = $_GET['codigoCategoria'] ?? null;
        
        if (!$codigoCategoria) {
            $this->respuestaJson['mensaje'] = 'Código de categoría requerido';
            $this->enviarRespuestaJson();
        }

        $productos = $this->modeloProducto->buscarProductosPorCategoria($codigoCategoria);
        
        $this->respuestaJson['exito'] = true;
        $this->respuestaJson['datos'] = $productos;
        $this->respuestaJson['mensaje'] = 'Productos obtenidos por categoría';
        
        $this->enviarRespuestaJson();
    }

    private function buscarProductosPorProveedor()
    {
        $codigoProveedor = $_GET['codigoProveedor'] ?? null;
        
        if (!$codigoProveedor) {
            $this->respuestaJson['mensaje'] = 'Código de proveedor requerido';
            $this->enviarRespuestaJson();
        }

        $productos = $this->modeloProducto->buscarProductosPorProveedor($codigoProveedor);
        
        $this->respuestaJson['exito'] = true;
        $this->respuestaJson['datos'] = $productos;
        $this->respuestaJson['mensaje'] = 'Productos obtenidos por proveedor';
        
        $this->enviarRespuestaJson();
    }

    private function crearProducto()
    {
        $datos = $this->obtenerDatosPeticion();
        $camposRequeridos = ['nombreProducto', 'descripcionProducto', 'stockProducto', 'precioProducto', 'codigoCategoria', 'codigoProveedor'];
        
        if (!$this->validarCamposRequeridos($camposRequeridos, $datos)) {
            $this->enviarRespuestaJson();
        }

        // Validar que la categoría existe
        $categoria = $this->modeloCategoria->buscarCategoriaPorCodigo($datos['codigoCategoria']);
        if (!$categoria) {
            $this->respuestaJson['mensaje'] = 'La categoría especificada no existe';
            $this->enviarRespuestaJson();
        }

        // Validar que el proveedor existe
        $proveedor = $this->modeloProveedor->buscarProveedorPorCodigo($datos['codigoProveedor']);
        if (!$proveedor) {
            $this->respuestaJson['mensaje'] = 'El proveedor especificado no existe';
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloProducto->insertarProducto(
            $datos['nombreProducto'],
            $datos['descripcionProducto'],
            $datos['stockProducto'],
            $datos['precioProducto'],
            $datos['codigoCategoria'],
            $datos['codigoProveedor'],
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Producto creado correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al crear el producto - Verifique los datos numéricos';
        }
        
        $this->enviarRespuestaJson();
    }

    private function actualizarProducto()
    {
        $datos = $this->obtenerDatosPeticion();
        $camposRequeridos = ['codigoProducto', 'nombreProducto', 'descripcionProducto', 'stockProducto', 'precioProducto', 'codigoCategoria', 'codigoProveedor'];
        
        if (!$this->validarCamposRequeridos($camposRequeridos, $datos)) {
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloProducto->actualizarProducto(
            $datos['codigoProducto'],
            $datos['nombreProducto'],
            $datos['descripcionProducto'],
            $datos['stockProducto'],
            $datos['precioProducto'],
            $datos['codigoCategoria'],
            $datos['codigoProveedor'],
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Producto actualizado correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al actualizar el producto';
        }
        
        $this->enviarRespuestaJson();
    }

    private function actualizarStockProducto()
    {
        $datos = $this->obtenerDatosPeticion();
        $camposRequeridos = ['codigoProducto', 'nuevoStock'];
        
        if (!$this->validarCamposRequeridos($camposRequeridos, $datos)) {
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloProducto->actualizarStockProducto(
            $datos['codigoProducto'],
            $datos['nuevoStock'],
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Stock actualizado correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al actualizar el stock - Verifique que sea un número válido';
        }
        
        $this->enviarRespuestaJson();
    }

    private function eliminarProducto()
    {
        $datos = $this->obtenerDatosPeticion();
        $codigoProducto = $datos['codigoProducto'] ?? null;
        
        if (!$codigoProducto) {
            $this->respuestaJson['mensaje'] = 'Código de producto requerido';
            $this->enviarRespuestaJson();
        }

        $resultado = $this->modeloProducto->eliminarProducto(
            $codigoProducto,
            $this->codigoUsuarioSesion
        );

        if ($resultado) {
            $this->respuestaJson['exito'] = true;
            $this->respuestaJson['mensaje'] = 'Producto eliminado correctamente';
        } else {
            $this->respuestaJson['mensaje'] = 'Error al eliminar el producto';
        }
        
        $this->enviarRespuestaJson();
    }
}
