<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/helpers.php';
require_login();

$stmt = db()->query("CALL sp_oficinas_list()");
$rows = $stmt->fetchAll();
$stmt->closeCursor();
json_ok($rows);
