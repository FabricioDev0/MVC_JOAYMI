<?php

require_once __DIR__ . '/ModeloBase.php';

class Rol extends ModeloBase
{
    private $codigoRol;
    private $nombreRol;
    private $fechaCreacion;
    private $estadoActivo;

    public function __construct()
    {
        parent::__construct('tm_roles', 'cod_rol');
    }

    // === GETTERS ===
    public function getCodigoRol() { return $this->codigoRol; }
    public function getNombreRol() { return $this->nombreRol; }
    public function getFechaCreacion() { return $this->fechaCreacion; }
    public function getEstadoActivo() { return $this->estadoActivo; }

    // === SETTERS ===
    public function setCodigoRol($codigoRol) { $this->codigoRol = $codigoRol; }
    public function setNombreRol($nombreRol) { $this->nombreRol = $nombreRol; }
    public function setEstadoActivo($estadoActivo) { $this->estadoActivo = $estadoActivo; }

    public function listarRoles()
    {
        return $this->ejecutarConsulta("SELECT * FROM vw_listar_roles ORDER BY cod_rol");
    }

    public function buscarRolPorCodigo($codigoRol)
    {
        $consulta = "SELECT * FROM tm_roles WHERE cod_rol = ? AND est_activo = 1";
        $resultado = $this->ejecutarConsulta($consulta, [$codigoRol]);
        
        if (!empty($resultado)) {
            $rol = $resultado[0];
            $this->mapearDatosDesdeBaseDatos($rol);
            return $rol;
        }
        
        return null;
    }

    public function existeRol($codigoRol)
    {
        return $this->existeRegistro($codigoRol);
    }

    private function mapearDatosDesdeBaseDatos($datosRol)
    {
        $this->codigoRol = $datosRol['cod_rol'];
        $this->nombreRol = $datosRol['str_nombre'];
        $this->fechaCreacion = $datosRol['fec_creacion'];
        $this->estadoActivo = $datosRol['est_activo'];
    }
}
