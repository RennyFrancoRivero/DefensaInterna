<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();

$id=(int)($_POST['id']??0); $nombre=trim($_POST['nombre']??'');
$rol=trim($_POST['rol']??''); $estado=($_POST['estado']??'Activo')==='Activo'?1:0;
$pwd=$_POST['password']??''; $pwd2=$_POST['password2']??'';
if($id<=0||$nombre===''||$rol==='') json_err('Datos inválidos');

$hash = null;
if($rol!=='Conductor' && $pwd!==''){
  if($pwd!==$pwd2||strlen($pwd)<8) json_err('Contraseña inválida');
  $hash = password_hash($pwd, PASSWORD_BCRYPT);
}
$stmt=db()->prepare("CALL sp_usuario_update(?,?,?,?,?)");
$stmt->execute([$id,$nombre,$rol,$estado,$hash]);
$out=$stmt->fetch(); $stmt->closeCursor();
json_ok($out);
