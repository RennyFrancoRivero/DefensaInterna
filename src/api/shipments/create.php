<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();

$cliente_id=(int)($_POST['cliente_id']??0);
$dn=trim($_POST['destinatario_nombres']??'');
$dt=trim($_POST['destinatario_telefono']??'');
$dd=trim($_POST['destinatario_direccion']??'');
$des=trim($_POST['descripcion_contenido']??'');
$peso=(float)($_POST['peso_kg']??0);
$valor=(float)($_POST['valor_declarado']??0);
$tipo_id=(int)($_POST['tipo_encomienda_id']??0);
$ruta_id=(int)($_POST['ruta_id']??0);
$vc_id=(int)($_POST['vehiculo_conductor_id']??0);
$ofi_o=(int)($_POST['oficina_origen_id']??0);
$ofi_d=(int)($_POST['oficina_destino_id']??0);
if($cliente_id<=0||$dn===''||$tipo_id<=0||$ruta_id<=0||$ofi_o<=0||$ofi_d<=0) json_err('Datos requeridos');

$pdo=db();
$stmt=$pdo->prepare("CALL sp_envio_create(?,?,?,?,?,?,?,?,?,?,?,?,?,?, @p_id, @p_codigo, @p_costo)");
$stmt->execute([$cliente_id,$dn,$dt,$dd,$des,$peso,$valor,$tipo_id,$ruta_id,$vc_id,$_SESSION['user']['id'],$ofi_o,$ofi_d]);
$stmt->closeCursor();
$row=$pdo->query("SELECT @p_id AS id, @p_codigo AS codigo, @p_costo AS costo")->fetch();
json_ok($row);
