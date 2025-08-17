<?php
// /src/config/db.php
function db(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $host = '127.0.0.1';
    $db   = 'trufix';
    $usr  = 'root';
    $pwd  = ''; // WAMP por defecto
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $usr, $pwd, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false, // <— recomendado
    ]);
  }
  return $pdo;
}
