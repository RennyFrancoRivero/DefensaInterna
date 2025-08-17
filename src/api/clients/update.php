<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();
$id=(int)($_POST['id']??0); $nombre=trim($_POST['nombre']??'');
$ci=trim($_POST['ci']??''); $tel=trim($_POST['telefono']??'');
$email=trim($_POST['email']??''); $estado=(int)($_POST['estado']??1);
if($id<=0||$nombre==='') json_err('Datos inválidos');
[$n,$a]=array_pad(explode(' ',$nombre,2),2,'');
$stmt=db()->prepare("CALL sp_cliente_update(?,?,?,?,?,?,?)");
$stmt->execute([$id,$ci,$n,$a,$tel,$email,$estado]);
$out=$stmt->fetch(); $stmt->closeCursor();
json_ok($out);
