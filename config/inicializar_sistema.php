<?php
/**
 * Script de inicialización del sistema JOAYMI
 * Crea directorios necesarios y verifica configuración
 */

// Crear directorio de logs si no existe
$directorioLogs = __DIR__ . '/../logs';
if (!is_dir($directorioLogs)) {
    mkdir($directorioLogs, 0755, true);
    
    // Crear archivos de log iniciales
    touch($directorioLogs . '/joaymi_error.log');
    touch($directorioLogs . '/joaymi_actividad.log');
    
    // Proteger directorio de logs
    file_put_contents($directorioLogs . '/.htaccess', "Order Allow,Deny\nDeny from all");
}

// Verificar permisos de escritura
if (!is_writable($directorioLogs)) {
    die('Error: El directorio de logs no tiene permisos de escritura');
}

echo " Sistema JOAYMI inicializado correctamente\n";
echo " Directorio de logs: $directorioLogs\n";
echo " Permisos configurados correctamente\n";
?>
