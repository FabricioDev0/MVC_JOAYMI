<?php

require_once __DIR__ . '/../config/Sistema.php';

abstract class ModeloBase
{
    protected $nombreTabla;
    protected $campoClavePrimaria;

    public function __construct($nombreTabla, $campoClavePrimaria)
    {
        $this->nombreTabla = $nombreTabla;
        $this->campoClavePrimaria = $campoClavePrimaria;
    }

    protected function ejecutarProcedimiento($nombreProcedimiento, $parametros = [])
    {
        try {
            Sistema::ejecutarProcedimiento($nombreProcedimiento, $parametros);
            return true;
        } catch (Exception $excepcion) {
            Sistema::registrarError($excepcion->getMessage(), get_class($this) . "::$nombreProcedimiento");
            return false;
        }
    }

    protected function obtenerTodosLosDatos($nombreProcedimiento, $parametros = [])
    {
        return Sistema::obtenerTodosDeProcedimiento($nombreProcedimiento, $parametros);
    }

    protected function obtenerUnDato($nombreProcedimiento, $parametros = [])
    {
        return Sistema::obtenerUnoDeProcedimiento($nombreProcedimiento, $parametros);
    }

    protected function ejecutarConsulta($consulta, $parametros = [])
    {
        return Sistema::ejecutarConsulta($consulta, $parametros);
    }

    protected function existeRegistro($valorClave)
    {
        $consulta = "SELECT COUNT(*) as total FROM {$this->nombreTabla} 
                    WHERE {$this->campoClavePrimaria} = ? AND est_activo = 1";
        
        $resultado = $this->ejecutarConsulta($consulta, [$valorClave]);
        return isset($resultado[0]) && $resultado[0]['total'] > 0;
    }

    protected function registrarActividad($accion, $registroId = null)
    {
        Sistema::registrarActividad($accion, $this->nombreTabla, $registroId);
    }
}
