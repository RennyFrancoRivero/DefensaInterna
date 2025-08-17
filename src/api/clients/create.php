<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();
$nombre=trim($_POST['nombre']??''); $ci=trim($_POST['ci']??'');
$tel=trim($_POST['telefono']??''); $email=trim($_POST['email']??'');
if($nombre==='') json_err('Nombre requerido');
[$nombres,$apellidos]=array_pad(explode(' ',$nombre,2),2,'');
$stmt=db()->prepare("CALL sp_cliente_create(?,?,?,?,?)");
$stmt->execute([$ci,$nombres,$apellidos,$tel,$email]);
$out=$stmt->fetch(); $stmt->closeCursor();
json_ok($out);
