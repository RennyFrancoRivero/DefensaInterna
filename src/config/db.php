<?php
function db(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $host = '127.0.0.1';
            $db   = 'trufix';
            $user = 'root'; 
            $pass = '';
            $charset = 'utf8mb4';
            
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $pdo = new PDO($dsn, $user, $pass, $options);
            
        } catch(PDOException $e) {
            // Log el error pero no mostrar detalles sensibles al usuario
            error_log("Error de conexión: " . $e->getMessage());
            throw new Exception('Error de conexión a la base de datos');
        }
    }
    
    return $pdo;
}
?>