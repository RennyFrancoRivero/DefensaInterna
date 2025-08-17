<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();

$envio_id=(int)($_POST['envio_id']??0);
$estado_id=(int)($_POST['estado_id']??0);
$oficina_id=(int)($_POST['oficina_id']??0);
$vc_id=(int)($_POST['vehiculo_conductor_id']??0);
$obs=trim($_POST['observaciones']??'');
if($envio_id<=0||$estado_id<=0) json_err('Datos inválidos');

$stmt=db()->prepare("CALL sp_envio_change_status(?,?,?,?,?,?)");
$stmt->execute([$envio_id,$estado_id,$_SESSION['user']['id'],$oficina_id,$vc_id,$obs]);
$out=$stmt->fetch(); $stmt->closeCursor();
json_ok($out);
