<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();

$usuario = trim($_POST['usuario'] ?? '');     // username (solo si no es conductor)
$nombre  = trim($_POST['nombre'] ?? '');      // nombre completo
$rol     = trim($_POST['rol'] ?? '');         // Administrador|Empleado|Conductor
$estado  = ($_POST['estado'] ?? 'Activo')==='Activo' ? 1 : 0;
$pwd     = $_POST['password'] ?? '';
$pwd2    = $_POST['password2'] ?? '';

if($nombre===''||$rol==='') json_err('Campos requeridos');
$hash = null;
if($rol!=='Conductor'){
  if($usuario===''||$pwd===''||$pwd!==$pwd2||strlen($pwd)<8) json_err('Credenciales inválidas');
  $hash = password_hash($pwd, PASSWORD_BCRYPT);
}
$stmt=db()->prepare("CALL sp_usuario_create(?,?,?,?,?)");
$stmt->execute([$nombre,$rol,$estado,$usuario,$hash]);
$out=$stmt->fetch(); $stmt->closeCursor();
json_ok($out);
