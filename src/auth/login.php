<?php
// NO pongas nada por fuera de este bloque PHP (ni espacios en blanco)
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  json_err('Método no permitido', 405);
}

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');

if ($username === '' || $password === '') {
  json_err('Usuario y contraseña requeridos', 422);
}

try {
  $pdo = db();

  $sql = "SELECT u.id, u.username, u.password_hash, u.nombres, u.apellidos,
                 u.has_login, u.estado, t.nombre AS rol
          FROM usuarios u
          JOIN tipos_usuario t ON t.id = u.tipo_usuario_id
          WHERE u.username = ? AND u.has_login = 1 AND u.estado = 1
          LIMIT 1";
  $st = $pdo->prepare($sql);
  $st->execute([$username]);
  $u = $st->fetch();

  $GENERIC = 'Usuario o contraseña incorrectos';
  if (!$u) json_err($GENERIC, 401);
  if (!password_verify($password, (string)$u['password_hash'])) json_err($GENERIC, 401);

  $_SESSION['user'] = [
    'id'       => (int)$u['id'],
    'username' => $u['username'],
    'name'     => trim(($u['nombres'] ?? '').' '.($u['apellidos'] ?? '')),
    'role'     => $u['rol'] ?? '',
  ];
  $_SESSION['user_id'] = (int)$u['id'];

  $pdo->prepare("UPDATE usuarios
                 SET ultimo_acceso = NOW(), intentos_fallidos = 0, bloqueado_hasta = NULL
                 WHERE id = ?")->execute([$_SESSION['user_id']]);

  json_ok(['redirect' => '/home.php']);
} catch (Throwable $e) {
  // Opcional: loggear $e->getMessage()
  json_err('No se pudo procesar el login', 500);
}
