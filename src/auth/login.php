<?php
// /src/auth/login.php
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

  // Solo usuarios con acceso (has_login=1) y activos (estado=1)
  $sql = "SELECT u.id, u.username, u.password_hash, u.nombres, u.apellidos,
                 u.has_login, u.estado, t.nombre AS rol
            FROM usuarios u
            JOIN tipos_usuario t ON t.id = u.tipo_usuario_id
           WHERE u.username = ? AND u.has_login = 1 AND u.estado = 1
           LIMIT 1";
  $st = $pdo->prepare($sql);
  $st->execute([$username]);
  $u = $st->fetch();

  // Mensaje genérico por seguridad
  $GENERIC = 'Usuario o contraseña incorrectos';

  if (!$u) {
    json_err($GENERIC, 401);
  }

  if (!password_verify($password, (string)$u['password_hash'])) {
    json_err($GENERIC, 401);
  }

  // OK: crear sesión con tu convención (helpers.require_login mira $_SESSION['user'])
  $_SESSION['user'] = [
    'id'       => (int)$u['id'],
    'username' => $u['username'],
    'name'     => trim(($u['nombres'] ?? '') . ' ' . ($u['apellidos'] ?? '')),
    'role'     => $u['rol'] ?? '',
  ];

  // (Opcional) además estos atajos:
  $_SESSION['user_id']   = (int)$u['id'];
  $_SESSION['user_name'] = $_SESSION['user']['name'];
  $_SESSION['user_role'] = $_SESSION['user']['role'];

  // Último acceso (opcional)
  $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW(), intentos_fallidos=0, bloqueado_hasta=NULL WHERE id=?")
      ->execute([$_SESSION['user_id']]);

  json_ok([
    'id'     => $_SESSION['user_id'],
    'nombre' => $_SESSION['user_name'],
    'rol'    => $_SESSION['user_role'],
  ]);

} catch (Throwable $e) {
  // En producción registra $e->getMessage()
  json_err('No se pudo procesar el login', 500);
}
