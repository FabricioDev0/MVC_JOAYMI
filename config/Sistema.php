<?php

/**
 * Clase principal de configuración del sistema JOAYMI
 * Versión corregida con mejor manejo de errores
 */
class Sistema
{
    // === CONFIGURACIÓN DE BASE DE DATOS ===
    private static $configBD = [
        'host' => 'localhost',
        'dbname' => 'ene29pro_joaymi',
        'username' => 'root',
        'password' => '',
        'port' => '3306',
        'charset' => 'utf8mb4'
    ];

    // === CONFIGURACIÓN DE SESIÓN ===
    const SESION_TIEMPO_VIDA = 3600; // 1 hora
    const SESION_NOMBRE = 'JOAYMI_SESSION';

    // === ROLES DEL SISTEMA ===
    const ROL_ADMINISTRADOR = 1;
    const ROL_USUARIO = 2;

    // === CONFIGURACIÓN DE SEGURIDAD ===
    const CLAVE_MINIMA_LONGITUD = 6;
    const INTENTOS_LOGIN_MAXIMOS = 5;

    private static $conexionBD = null;

    /**
     * Obtiene la conexión singleton a la base de datos
     */
    public static function obtenerConexionBD()
    {
        if (self::$conexionBD === null) {
            try {
                $config = self::$configBD;
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
                
                self::$conexionBD = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
                
            } catch (PDOException $error) {
                self::registrarError("Error de conexión BD: " . $error->getMessage());
                throw new Exception("Error al conectar con la base de datos");
            }
        }
        
        return self::$conexionBD;
    }

    /**
     * Ejecuta un procedimiento almacenado
     */
    public static function ejecutarProcedimiento($nombreProcedimiento, array $parametros = [])
    {
        $conexion = self::obtenerConexionBD();
        
        try {
            if (empty($parametros)) {
                $stmt = $conexion->prepare("CALL $nombreProcedimiento()");
            } else {
                $placeholders = str_repeat('?,', count($parametros) - 1) . '?';
                $stmt = $conexion->prepare("CALL $nombreProcedimiento($placeholders)");
            }
            
            $stmt->execute($parametros);
            return $stmt;
            
        } catch (PDOException $e) {
            self::registrarError("Error en SP $nombreProcedimiento: " . $e->getMessage());
            throw new Exception("Error ejecutando procedimiento: $nombreProcedimiento");
        }
    }

    /**
     * Ejecuta procedimiento y retorna todos los resultados
     */
    public static function obtenerTodosDeProcedimiento($nombreProcedimiento, array $parametros = [])
    {
        try {
            $stmt = self::ejecutarProcedimiento($nombreProcedimiento, $parametros);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            self::registrarError("Error obteniendo datos de $nombreProcedimiento: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ejecuta procedimiento y retorna un solo resultado
     */
    public static function obtenerUnoDeProcedimiento($nombreProcedimiento, array $parametros = [])
    {
        try {
            $stmt = self::ejecutarProcedimiento($nombreProcedimiento, $parametros);
            return $stmt->fetch() ?: null;
        } catch (Exception $e) {
            self::registrarError("Error obteniendo dato de $nombreProcedimiento: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ejecuta consulta SQL directa
     */
    public static function ejecutarConsulta($consulta, array $parametros = [])
    {
        try {
            $conexion = self::obtenerConexionBD();
            $stmt = $conexion->prepare($consulta);
            $stmt->execute($parametros);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            self::registrarError("Error en consulta SQL: " . $e->getMessage());
            return [];
        }
    }

    // === UTILIDADES DE VALIDACIÓN ===

    /**
     * Valida campos requeridos en un array de datos
     */
    public static function validarCamposRequeridos($camposRequeridos, $datos)
    {
        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo]) || empty(trim($datos[$campo]))) {
                return [
                    'valido' => false,
                    'mensaje' => "El campo '$campo' es obligatorio"
                ];
            }
        }
        
        return ['valido' => true, 'mensaje' => ''];
    }

    /**
     * Valida formato de correo electrónico
     */
    public static function validarEmail($correo)
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida que un número sea positivo
     */
    public static function validarNumeroPositivo($numero)
    {
        return is_numeric($numero) && $numero > 0;
    }

    /**
     * Valida que un número sea mayor o igual a cero
     */
    public static function validarNumeroNoNegativo($numero)
    {
        return is_numeric($numero) && $numero >= 0;
    }

    /**
     * Sanitiza texto para prevenir XSS
     */
    public static function sanitizarTexto($texto)
    {
        return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
    }

    // === UTILIDADES DE RESPUESTA ===

    /**
     * Genera respuesta JSON estandarizada
     */
    public static function generarRespuesta($exito, $mensaje, $datos = null)
    {
        return [
            'exito' => $exito,
            'mensaje' => $mensaje,
            'datos' => $datos,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Envía respuesta JSON y termina ejecución
     */
    public static function enviarRespuestaJson($respuesta)
    {
        // Limpiar output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // === UTILIDADES DE SESIÓN ===

    /**
     * Inicia sesión si no está activa
     */
    public static function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(self::SESION_NOMBRE);
            session_start();
        }
    }

    /**
     * Verifica si el usuario está autenticado
     */
    public static function usuarioAutenticado()
    {
        self::iniciarSesion();
        return isset($_SESSION['cod_usuario']) && !empty($_SESSION['cod_usuario']);
    }

    /**
     * Verifica si el usuario es administrador
     */
    public static function esAdministrador()
    {
        self::iniciarSesion();
        return isset($_SESSION['cod_rol']) && $_SESSION['cod_rol'] == self::ROL_ADMINISTRADOR;
    }

    /**
     * Obtiene el código del usuario en sesión
     */
    public static function obtenerUsuarioSesion()
    {
        self::iniciarSesion();
        return $_SESSION['cod_usuario'] ?? null;
    }

    /**
     * Cierra la sesión del usuario
     */
    public static function cerrarSesion()
    {
        self::iniciarSesion();
        session_destroy();
    }

    // === UTILIDADES DE LOG ===

    /**
     * Registra errores en el log del sistema
     */
    public static function registrarError($mensaje, $contexto = '')
    {
        $fecha = date('Y-m-d H:i:s');
        $contextoStr = $contexto ? " [$contexto]" : '';
        $logMessage = "[$fecha] ERROR$contextoStr: $mensaje" . PHP_EOL;
        
        // Crear directorio de logs si no existe
        $directorioLogs = __DIR__ . '/../logs';
        if (!is_dir($directorioLogs)) {
            mkdir($directorioLogs, 0755, true);
        }
        
        error_log($logMessage, 3, $directorioLogs . '/joaymi_error.log');
    }

    /**
     * Registra actividad del usuario para auditoría
     */
    public static function registrarActividad($accion, $tabla, $registroId = null)
    {
        $fecha = date('Y-m-d H:i:s');
        $usuario = self::obtenerUsuarioSesion() ?? 'SISTEMA';
        $registro = $registroId ? " ID:$registroId" : '';
        
        $logMessage = "[$fecha] ACTIVIDAD - Usuario:$usuario | Acción:$accion | Tabla:$tabla$registro" . PHP_EOL;
        
        // Crear directorio de logs si no existe
        $directorioLogs = __DIR__ . '/../logs';
        if (!is_dir($directorioLogs)) {
            mkdir($directorioLogs, 0755, true);
        }
        
        error_log($logMessage, 3, $directorioLogs . '/joaymi_actividad.log');
    }
}
