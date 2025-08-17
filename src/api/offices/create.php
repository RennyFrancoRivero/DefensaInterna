<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();

$nombre = trim($_POST['nombre'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$ciudad_id = (int)($_POST['ciudad_id'] ?? 0);
if ($nombre==='' || $direccion==='' || $ciudad_id<=0) json_err('Datos requeridos');

$stmt = db()->prepare("CALL sp_oficina_create(?,?,?,?)");
$stmt->execute([$nombre,$direccion,$telefono,$ciudad_id]);
$data = $stmt->fetch(); $stmt->closeCursor();
json_ok($data);
