<?php

require_once __DIR__ . '/ModeloBase.php';

class Usuario extends ModeloBase
{
    private $codigoUsuario;
    private $correoElectronico;
    private $claveAcceso;
    private $codigoRol;
    private $estadoActivo;
    private $fechaCreacion;
    private $fechaActualizacion;
    private $fechaEliminacion;
    private $codigoUsuarioCreador;
    private $codigoUsuarioModificador;

    public function __construct()
    {
        parent::__construct('tm_usuarios', 'cod_usuario');
    }

    // === GETTERS ===
    public function getCodigoUsuario() { return $this->codigoUsuario; }
    public function getCorreoElectronico() { return $this->correoElectronico; }
    public function getCodigoRol() { return $this->codigoRol; }
    public function getEstadoActivo() { return $this->estadoActivo; }

    // === SETTERS ===
    public function setCodigoUsuario($codigoUsuario) { $this->codigoUsuario = $codigoUsuario; }
    public function setCorreoElectronico($correoElectronico) { $this->correoElectronico = $correoElectronico; }
    public function setCodigoRol($codigoRol) { $this->codigoRol = $codigoRol; }
    public function setEstadoActivo($estadoActivo) { $this->estadoActivo = $estadoActivo; }

    public function insertarUsuario($correoElectronico, $claveAcceso, $codigoRol, $codigoUsuarioCreador)
    {
        if (!Sistema::validarEmail($correoElectronico)) {
            return false;
        }

        if (strlen($claveAcceso) < Sistema::CLAVE_MINIMA_LONGITUD) {
            return false;
        }

        if ($this->existeCorreoElectronico($correoElectronico)) {
            return false;
        }

        $resultado = $this->ejecutarProcedimiento('InsertarUsuario', [
            $correoElectronico,
            $claveAcceso,
            $codigoRol,
            $codigoUsuarioCreador
        ]);

        if ($resultado) {
            $this->registrarActividad('CREAR');
        }

        return $resultado;
    }

    public function listarUsuarios()
    {
        return $this->obtenerTodosLosDatos('ListarUsuarios');
    }

    public function buscarUsuarioPorCodigo($codigoUsuario)
    {
        $resultado = $this->obtenerUnDato('BuscarUsuarioPorId', [$codigoUsuario]);
        
        if ($resultado) {
            $this->mapearDatosDesdeBaseDatos($resultado);
        }
        
        return $resultado;
    }

    public function actualizarUsuario($codigoUsuario, $correoElectronico, $codigoRol, $codigoUsuarioModificador)
    {
        if (!Sistema::validarEmail($correoElectronico)) {
            return false;
        }

        if ($this->existeCorreoElectronico($correoElectronico, $codigoUsuario)) {
            return false;
        }

        $resultado = $this->ejecutarProcedimiento('ActualizarUsuario', [
            $codigoUsuario,
            $correoElectronico,
            $codigoRol,
            $codigoUsuarioModificador
        ]);

        if ($resultado) {
            $this->registrarActividad('ACTUALIZAR', $codigoUsuario);
        }

        return $resultado;
    }

    public function eliminarUsuario($codigoUsuario, $codigoUsuarioModificador)
    {
        $resultado = $this->ejecutarProcedimiento('EliminarUsuario', [
            $codigoUsuario,
            $codigoUsuarioModificador
        ]);

        if ($resultado) {
            $this->registrarActividad('ELIMINAR', $codigoUsuario);
        }

        return $resultado;
    }

    public function autenticarUsuario($correoElectronico, $claveAcceso)
    {
        $usuario = $this->obtenerUnDato('LoginUsuario', [$correoElectronico]);
        
        if ($usuario && $claveAcceso === $usuario['str_clave']) {
            $this->mapearDatosDesdeBaseDatos($usuario);
            $this->registrarActividad('LOGIN', $usuario['cod_usuario']);
            return $usuario;
        }
        
        return null;
    }

    public function existeCorreoElectronico($correoElectronico, $excluirUsuario = null)
    {
        $consulta = "SELECT COUNT(*) as total FROM tm_usuarios 
                    WHERE str_correo = ? AND est_activo = 1";
        $parametros = [$correoElectronico];
        
        if ($excluirUsuario) {
            $consulta .= " AND cod_usuario != ?";
            $parametros[] = $excluirUsuario;
        }
        
        $resultado = $this->ejecutarConsulta($consulta, $parametros);
        return isset($resultado[0]) && $resultado[0]['total'] > 0;
    }

    public function esAdministrador($codigoUsuario)
    {
        $usuario = $this->buscarUsuarioPorCodigo($codigoUsuario);
        return $usuario && $usuario['cod_rol'] == Sistema::ROL_ADMINISTRADOR;
    }

    private function mapearDatosDesdeBaseDatos($datosUsuario)
    {
        $this->codigoUsuario = $datosUsuario['cod_usuario'];
        $this->correoElectronico = $datosUsuario['str_correo'];
        $this->claveAcceso = $datosUsuario['str_clave'];
        $this->codigoRol = $datosUsuario['cod_rol'];
        $this->estadoActivo = $datosUsuario['est_activo'];
        $this->fechaCreacion = $datosUsuario['fec_creacion'];
        $this->fechaActualizacion = $datosUsuario['fec_actualizacion'] ?? null;
        $this->fechaEliminacion = $datosUsuario['fec_eliminacion'] ?? null;
        $this->codigoUsuarioCreador = $datosUsuario['cod_usuario_creador'] ?? null;
        $this->codigoUsuarioModificador = $datosUsuario['cod_usuario_modificador'] ?? null;
    }
}
