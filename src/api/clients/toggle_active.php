<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();
$id=(int)($_POST['id']??0); $a=(int)($_POST['activo']??0);
if($id<=0) json_err('ID inválido');
$stmt=db()->prepare("CALL sp_cliente_toggle(?,?)");
$stmt->execute([$id,$a]); $out=$stmt->fetch(); $stmt->closeCursor();
json_ok($out);
