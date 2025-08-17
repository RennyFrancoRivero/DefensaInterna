<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();
$id=(int)($_POST['id']??0); $nombre=trim($_POST['nombre']??'');
$orden=(int)($_POST['orden']??0); $ini=(int)($_POST['inicial']??0);
$fin=(int)($_POST['final']??0); $estado=(int)($_POST['estado']??1);
if($id<=0||$nombre===''||$orden<=0) json_err('Datos inválidos');
$stmt=db()->prepare("CALL sp_estado_update(?,?,?,?,?,?)");
$stmt->execute([$id,$nombre,$orden,$ini,$fin,$estado]);
$out=$stmt->fetch(); $stmt->closeCursor();
json_ok($out);
