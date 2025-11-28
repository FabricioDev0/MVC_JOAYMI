<?php

require_once __DIR__ . '/ModeloBase.php';

class Producto extends ModeloBase
{
    private $codigoProducto;
    private $nombreProducto;
    private $descripcionProducto;
    private $stockProducto;
    private $precioProducto;
    private $codigoCategoria;
    private $codigoProveedor;
    private $estadoActivo;
    private $fechaCreacion;
    private $fechaActualizacion;
    private $fechaEliminacion;
    private $codigoUsuarioCreador;
    private $codigoUsuarioModificador;

    public function __construct()
    {
        parent::__construct('tm_productos', 'cod_producto');
    }

    // === GETTERS ===
    public function getCodigoProducto() { return $this->codigoProducto; }
    public function getNombreProducto() { return $this->nombreProducto; }
    public function getDescripcionProducto() { return $this->descripcionProducto; }
    public function getStockProducto() { return $this->stockProducto; }
    public function getPrecioProducto() { return $this->precioProducto; }
    public function getCodigoCategoria() { return $this->codigoCategoria; }
    public function getCodigoProveedor() { return $this->codigoProveedor; }
    public function getEstadoActivo() { return $this->estadoActivo; }

    // === SETTERS ===
    public function setCodigoProducto($codigoProducto) { $this->codigoProducto = $codigoProducto; }
    public function setNombreProducto($nombreProducto) { $this->nombreProducto = $nombreProducto; }
    public function setDescripcionProducto($descripcionProducto) { $this->descripcionProducto = $descripcionProducto; }
    public function setStockProducto($stockProducto) { $this->stockProducto = $stockProducto; }
    public function setPrecioProducto($precioProducto) { $this->precioProducto = $precioProducto; }
    public function setCodigoCategoria($codigoCategoria) { $this->codigoCategoria = $codigoCategoria; }
    public function setCodigoProveedor($codigoProveedor) { $this->codigoProveedor = $codigoProveedor; }
    public function setEstadoActivo($estadoActivo) { $this->estadoActivo = $estadoActivo; }

    public function insertarProducto($nombreProducto, $descripcionProducto, $stockProducto, $precioProducto, $codigoCategoria, $codigoProveedor, $codigoUsuarioCreador)
    {
        if (!Sistema::validarNumeroNoNegativo($stockProducto)) {
            return false;
        }

        if (!Sistema::validarNumeroPositivo($precioProducto)) {
            return false;
        }

        $resultado = $this->ejecutarProcedimiento('InsertarProducto', [
            Sistema::sanitizarTexto($nombreProducto),
            Sistema::sanitizarTexto($descripcionProducto),
            (int)$stockProducto,
            (float)$precioProducto,
            $codigoCategoria,
            $codigoProveedor,
            $codigoUsuarioCreador
        ]);

        if ($resultado) {
            $this->registrarActividad('CREAR');
        }

        return $resultado;
    }

    public function listarProductos()
    {
        $consulta = "SELECT p.cod_producto, p.str_nombre, p.str_descripcion, p.int_stock, 
                        p.dec_precio, p.cod_categoria, p.cod_proveedor, p.est_activo, 
                        p.fec_creacion, p.fec_actualizacion, p.fec_eliminacion,
                        p.cod_usuario_creador, p.cod_usuario_modificador,
                        c.str_nombre AS categoria, 
                        pr.str_nombre AS proveedor
                 FROM tm_productos p
                 LEFT JOIN tm_categorias c ON p.cod_categoria = c.cod_categoria
                 LEFT JOIN tm_proveedores pr ON p.cod_proveedor = pr.cod_proveedor
                 WHERE p.est_activo = 1
                 ORDER BY p.str_nombre";
    
        return $this->ejecutarConsulta($consulta);
    }

    public function buscarProductoPorCodigo($codigoProducto)
    {
        $consulta = "SELECT p.cod_producto, p.str_nombre, p.str_descripcion, p.int_stock, 
                        p.dec_precio, p.cod_categoria, p.cod_proveedor, p.est_activo, 
                        p.fec_creacion, p.fec_actualizacion, p.fec_eliminacion,
                        p.cod_usuario_creador, p.cod_usuario_modificador,
                        c.str_nombre AS categoria, 
                        pr.str_nombre AS proveedor
                 FROM tm_productos p
                 LEFT JOIN tm_categorias c ON p.cod_categoria = c.cod_categoria
                 LEFT JOIN tm_proveedores pr ON p.cod_proveedor = pr.cod_proveedor
                 WHERE p.cod_producto = ?";

        $resultados = $this->ejecutarConsulta($consulta, [$codigoProducto]);

        if ($resultados && count($resultados) > 0) {
            $resultado = $resultados[0];
            $this->mapearDatosDesdeBaseDatos($resultado);
            return $resultado;
        }

        return null;
    }

    public function actualizarProducto($codigoProducto, $nombreProducto, $descripcionProducto, $stockProducto, $precioProducto, $codigoCategoria, $codigoProveedor, $codigoUsuarioModificador)
    {
        if (!Sistema::validarNumeroNoNegativo($stockProducto)) {
            return false;
        }

        if (!Sistema::validarNumeroPositivo($precioProducto)) {
            return false;
        }

        $resultado = $this->ejecutarProcedimiento('ActualizarProducto', [
            $codigoProducto,
            Sistema::sanitizarTexto($nombreProducto),
            Sistema::sanitizarTexto($descripcionProducto),
            (int)$stockProducto,
            (float)$precioProducto,
            $codigoCategoria,
            $codigoProveedor,
            $codigoUsuarioModificador
        ]);

        if ($resultado) {
            $this->registrarActividad('ACTUALIZAR', $codigoProducto);
        }

        return $resultado;
    }

    public function eliminarProducto($codigoProducto, $codigoUsuarioModificador)
    {
        $resultado = $this->ejecutarProcedimiento('EliminarProducto', [
            $codigoProducto,
            $codigoUsuarioModificador
        ]);

        if ($resultado) {
            $this->registrarActividad('ELIMINAR', $codigoProducto);
        }

        return $resultado;
    }

    public function buscarProductosPorCategoria($codigoCategoria)
    {
        $consulta = "SELECT p.*, c.str_nombre AS categoria, pr.str_nombre AS proveedor
                    FROM tm_productos p
                    JOIN tm_categorias c ON p.cod_categoria = c.cod_categoria
                    JOIN tm_proveedores pr ON p.cod_proveedor = pr.cod_proveedor
                    WHERE p.cod_categoria = ? AND p.est_activo = 1
                    ORDER BY p.str_nombre";
        
        return $this->ejecutarConsulta($consulta, [$codigoCategoria]);
    }

    public function buscarProductosPorProveedor($codigoProveedor)
    {
        $consulta = "SELECT p.*, c.str_nombre AS categoria, pr.str_nombre AS proveedor
                    FROM tm_productos p
                    JOIN tm_categorias c ON p.cod_categoria = c.cod_categoria
                    JOIN tm_proveedores pr ON p.cod_proveedor = pr.cod_proveedor
                    WHERE p.cod_proveedor = ? AND p.est_activo = 1
                    ORDER BY p.str_nombre";
        
        return $this->ejecutarConsulta($consulta, [$codigoProveedor]);
    }

    public function actualizarStockProducto($codigoProducto, $nuevoStock, $codigoUsuarioModificador)
    {
        if (!Sistema::validarNumeroNoNegativo($nuevoStock)) {
            return false;
        }

        $consulta = "UPDATE tm_productos 
                    SET int_stock = ?, cod_usuario_modificador = ?
                    WHERE cod_producto = ?";
        
        $resultado = $this->ejecutarConsulta($consulta, [$nuevoStock, $codigoUsuarioModificador, $codigoProducto]);
        
        if ($resultado !== false) {
            $this->registrarActividad('ACTUALIZAR_STOCK', $codigoProducto);
            return true;
        }
        
        return false;
    }

    private function mapearDatosDesdeBaseDatos($datosProducto)
    {
        $this->codigoProducto = $datosProducto['cod_producto'];
        $this->nombreProducto = $datosProducto['str_nombre'];
        $this->descripcionProducto = $datosProducto['str_descripcion'];
        $this->stockProducto = $datosProducto['int_stock'];
        $this->precioProducto = $datosProducto['dec_precio'];
        $this->codigoCategoria = $datosProducto['cod_categoria'];
        $this->codigoProveedor = $datosProducto['cod_proveedor'];
        $this->estadoActivo = $datosProducto['est_activo'];
        $this->fechaCreacion = $datosProducto['fec_creacion'];
        $this->fechaActualizacion = $datosProducto['fec_actualizacion'] ?? null;
        $this->fechaEliminacion = $datosProducto['fec_eliminacion'] ?? null;
        $this->codigoUsuarioCreador = $datosProducto['cod_usuario_creador'] ?? null;
        $this->codigoUsuarioModificador = $datosProducto['cod_usuario_modificador'] ?? null;
    }
}
