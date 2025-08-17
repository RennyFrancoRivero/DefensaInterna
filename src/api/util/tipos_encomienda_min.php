<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../helpers.php';

try {
  $pdo = db();
  $stmt = $pdo->query("SELECT id, nombre FROM tipos_encomienda WHERE estado=1 ORDER BY nombre");
  $rows = $stmt->fetchAll();
  json_ok($rows);
} catch (Exception $e) {
  json_err($e->getMessage(), 500);
}
