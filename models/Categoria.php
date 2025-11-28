<?php

require_once __DIR__ . '/ModeloBase.php';

class Categoria extends ModeloBase
{
    private $codigoCategoria;
    private $nombreCategoria;
    private $descripcionCategoria;
    private $estadoActivo;
    private $fechaCreacion;
    private $fechaActualizacion;
    private $fechaEliminacion;
    private $codigoUsuarioCreador;
    private $codigoUsuarioModificador;

    public function __construct()
    {
        parent::__construct('tm_categorias', 'cod_categoria');
    }

    // === GETTERS ===
    public function getCodigoCategoria() { return $this->codigoCategoria; }
    public function getNombreCategoria() { return $this->nombreCategoria; }
    public function getDescripcionCategoria() { return $this->descripcionCategoria; }
    public function getEstadoActivo() { return $this->estadoActivo; }

    // === SETTERS ===
    public function setCodigoCategoria($codigoCategoria) { $this->codigoCategoria = $codigoCategoria; }
    public function setNombreCategoria($nombreCategoria) { $this->nombreCategoria = $nombreCategoria; }
    public function setDescripcionCategoria($descripcionCategoria) { $this->descripcionCategoria = $descripcionCategoria; }
    public function setEstadoActivo($estadoActivo) { $this->estadoActivo = $estadoActivo; }

    public function insertarCategoria($nombreCategoria, $descripcionCategoria, $codigoUsuarioCreador)
    {
        $resultado = $this->ejecutarProcedimiento('InsertarCategoria', [
            Sistema::sanitizarTexto($nombreCategoria),
            Sistema::sanitizarTexto($descripcionCategoria),
            $codigoUsuarioCreador
        ]);

        if ($resultado) {
            $this->registrarActividad('CREAR');
        }

        return $resultado;
    }

    public function listarCategorias()
    {
        return $this->obtenerTodosLosDatos('ListarCategorias');
    }

    public function buscarCategoriaPorCodigo($codigoCategoria)
    {
        $resultado = $this->obtenerUnDato('BuscarCategoriaPorId', [$codigoCategoria]);
        
        if ($resultado) {
            $this->mapearDatosDesdeBaseDatos($resultado);
        }
        
        return $resultado;
    }

    public function actualizarCategoria($codigoCategoria, $nombreCategoria, $descripcionCategoria, $codigoUsuarioModificador)
    {
        $resultado = $this->ejecutarProcedimiento('ActualizarCategoria', [
            $codigoCategoria,
            Sistema::sanitizarTexto($nombreCategoria),
            Sistema::sanitizarTexto($descripcionCategoria),
            $codigoUsuarioModificador
        ]);

        if ($resultado) {
            $this->registrarActividad('ACTUALIZAR', $codigoCategoria);
        }

        return $resultado;
    }

    public function eliminarCategoria($codigoCategoria, $codigoUsuarioModificador)
    {
        $resultado = $this->ejecutarProcedimiento('EliminarCategoria', [
            $codigoCategoria,
            $codigoUsuarioModificador
        ]);

        if ($resultado) {
            $this->registrarActividad('ELIMINAR', $codigoCategoria);
        }

        return $resultado;
    }

    private function mapearDatosDesdeBaseDatos($datosCategoria)
    {
        $this->codigoCategoria = $datosCategoria['cod_categoria'];
        $this->nombreCategoria = $datosCategoria['str_nombre'];
        $this->descripcionCategoria = $datosCategoria['str_descripcion'];
        $this->estadoActivo = $datosCategoria['est_activo'];
        $this->fechaCreacion = $datosCategoria['fec_creacion'];
        $this->fechaActualizacion = $datosCategoria['fec_actualizacion'] ?? null;
        $this->fechaEliminacion = $datosCategoria['fec_eliminacion'] ?? null;
        $this->codigoUsuarioCreador = $datosCategoria['cod_usuario_creador'] ?? null;
        $this->codigoUsuarioModificador = $datosCategoria['cod_usuario_modificador'] ?? null;
    }
}
