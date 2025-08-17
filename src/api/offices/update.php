<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();

$id=(int)($_POST['id']??0);
$nombre=trim($_POST['nombre']??'');
$direccion=trim($_POST['direccion']??'');
$telefono=trim($_POST['telefono']??'');
$ciudad_id=(int)($_POST['ciudad_id']??0);
$estado=(int)($_POST['estado']??1);
if($id<=0||$nombre===''||$direccion===''||$ciudad_id<=0) json_err('Datos inválidos');

$stmt=db()->prepare("CALL sp_oficina_update(?,?,?,?,?,?)");
$stmt->execute([$id,$nombre,$direccion,$telefono,$ciudad_id,$estado]);
$out=$stmt->fetch(); $stmt->closeCursor();
json_ok($out);
