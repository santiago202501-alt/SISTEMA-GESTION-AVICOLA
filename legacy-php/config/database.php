<?php
/**
 * SIGA - Configuración de Base de Datos
 * Conexión PDO con manejo de errores
 */

define('DB_HOST',    'localhost');
define('DB_NAME',    'siga_avicola');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME',    'SIGA');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/SIGA');

// Umbrales de alertas
define('ALERTA_AMONIACO_PPM',  25);   // ppm máximo permitido
define('ALERTA_AGUA_LITROS',   800);  // litros mínimos por día
define('ALERTA_ALIMENTO_KG',   300);  // kg mínimos por día

/**
 * Retorna la conexión PDO singleton
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En producción, loguear el error real
            die(json_encode(['error' => 'Error de conexión a la base de datos.']));
        }
    }
    return $pdo;
}
