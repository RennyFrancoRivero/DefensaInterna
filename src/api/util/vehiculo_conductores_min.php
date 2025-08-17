<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../helpers.php';

try {
  $pdo = db();
  // JOIN para mostrar algo entendible
  $sql = "SELECT vc.id, CONCAT(v.placa,' - ',u.nombres,' ',u.apellidos) AS nombre
          FROM vehiculo_conductores vc
          JOIN vehiculos v ON vc.vehiculo_id = v.id
          JOIN usuarios u ON vc.conductor_id = u.id
          WHERE vc.estado=1
          ORDER BY v.placa";
  $stmt = $pdo->query($sql);
  $rows = $stmt->fetchAll();
  json_ok($rows);
} catch (Exception $e) {
  json_err($e->getMessage(), 500);
}
