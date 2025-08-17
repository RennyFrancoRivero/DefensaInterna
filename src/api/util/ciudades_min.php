<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../helpers.php';

try {
  $pdo = db();
  // ⚠️ si tenés un SP: $stmt = $pdo->query("CALL sp_ciudades_list()");
  $stmt = $pdo->query("SELECT id, nombre FROM ciudades WHERE estado=1 ORDER BY nombre");
  $rows = $stmt->fetchAll();
  json_ok($rows);
} catch (Exception $e) {
  json_err($e->getMessage(), 500);
}
