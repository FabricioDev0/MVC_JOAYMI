<?php

require_once __DIR__ . '/ModeloBase.php';

class Proveedor extends ModeloBase
{
    private $codigoProveedor;
    private $nombreProveedor;
    private $contactoProveedor;
    private $telefonoProveedor;
    private $estadoActivo;
    private $fechaCreacion;
    private $fechaActualizacion;
    private $fechaEliminacion;
    private $codigoUsuarioCreador;
    private $codigoUsuarioModificador;

    public function __construct()
    {
        parent::__construct('tm_proveedores', 'cod_proveedor');
    }

    // === GETTERS ===
    public function getCodigoProveedor() { return $this->codigoProveedor; }
    public function getNombreProveedor() { return $this->nombreProveedor; }
    public function getContactoProveedor() { return $this->contactoProveedor; }
    public function getTelefonoProveedor() { return $this->telefonoProveedor; }
    public function getEstadoActivo() { return $this->estadoActivo; }

    // === SETTERS ===
    public function setCodigoProveedor($codigoProveedor) { $this->codigoProveedor = $codigoProveedor; }
    public function setNombreProveedor($nombreProveedor) { $this->nombreProveedor = $nombreProveedor; }
    public function setContactoProveedor($contactoProveedor) { $this->contactoProveedor = $contactoProveedor; }
    public function setTelefonoProveedor($telefonoProveedor) { $this->telefonoProveedor = $telefonoProveedor; }
    public function setEstadoActivo($estadoActivo) { $this->estadoActivo = $estadoActivo; }

    public function insertarProveedor($nombreProveedor, $contactoProveedor, $telefonoProveedor, $codigoUsuarioCreador)
    {
        $resultado = $this->ejecutarProcedimiento('InsertarProveedor', [
            Sistema::sanitizarTexto($nombreProveedor),
            Sistema::sanitizarTexto($contactoProveedor),
            Sistema::sanitizarTexto($telefonoProveedor),
            $codigoUsuarioCreador
        ]);

        if ($resultado) {
            $this->registrarActividad('CREAR');
        }

        return $resultado;
    }

    public function listarProveedores()
    {
        return $this->obtenerTodosLosDatos('ListarProveedores');
    }

    public function buscarProveedorPorCodigo($codigoProveedor)
    {
        $resultado = $this->obtenerUnDato('BuscarProveedorPorId', [$codigoProveedor]);
        
        if ($resultado) {
            $this->mapearDatosDesdeBaseDatos($resultado);
        }
        
        return $resultado;
    }

    public function actualizarProveedor($codigoProveedor, $nombreProveedor, $contactoProveedor, $telefonoProveedor, $codigoUsuarioModificador)
    {
        $resultado = $this->ejecutarProcedimiento('ActualizarProveedor', [
            $codigoProveedor,
            Sistema::sanitizarTexto($nombreProveedor),
            Sistema::sanitizarTexto($contactoProveedor),
            Sistema::sanitizarTexto($telefonoProveedor),
            $codigoUsuarioModificador
        ]);

        if ($resultado) {
            $this->registrarActividad('ACTUALIZAR', $codigoProveedor);
        }

        return $resultado;
    }

    public function eliminarProveedor($codigoProveedor, $codigoUsuarioModificador)
    {
        $resultado = $this->ejecutarProcedimiento('EliminarProveedor', [
            $codigoProveedor,
            $codigoUsuarioModificador
        ]);

        if ($resultado) {
            $this->registrarActividad('ELIMINAR', $codigoProveedor);
        }

        return $resultado;
    }

    private function mapearDatosDesdeBaseDatos($datosProveedor)
    {
        $this->codigoProveedor = $datosProveedor['cod_proveedor'];
        $this->nombreProveedor = $datosProveedor['str_nombre'];
        $this->contactoProveedor = $datosProveedor['str_contacto'];
        $this->telefonoProveedor = $datosProveedor['str_telefono'];
        $this->estadoActivo = $datosProveedor['est_activo'];
        $this->fechaCreacion = $datosProveedor['fec_creacion'];
        $this->fechaActualizacion = $datosProveedor['fec_actualizacion'] ?? null;
        $this->fechaEliminacion = $datosProveedor['fec_eliminacion'] ?? null;
        $this->codigoUsuarioCreador = $datosProveedor['cod_usuario_creador'] ?? null;
        $this->codigoUsuarioModificador = $datosProveedor['cod_usuario_modificador'] ?? null;
    }
}
