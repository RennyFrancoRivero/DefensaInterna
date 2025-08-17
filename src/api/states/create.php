<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();
$codigo=trim($_POST['codigo']??''); $nombre=trim($_POST['nombre']??'');
$orden=(int)($_POST['orden']??0); $ini=(int)($_POST['inicial']??0); $fin=(int)($_POST['final']??0);
if($codigo===''||$nombre===''||$orden<=0) json_err('Datos requeridos');
$stmt=db()->prepare("CALL sp_estado_create(?,?,?,?,?)");
$stmt->execute([$codigo,$nombre,$orden,$ini,$fin]);
$out=$stmt->fetch(); $stmt->closeCursor();
json_ok($out);
