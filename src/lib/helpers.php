<?php
function json_ok($data = [], $code = 200) {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>true,'data'=>$data], JSON_UNESCAPED_UNICODE);
  exit;
}
function json_err($msg, $code = 400) {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false,'error'=>$msg], JSON_UNESCAPED_UNICODE);
  exit;
}
function require_login() {
  session_start();
  if (empty($_SESSION['user'])) json_err('No autenticado', 401);
}
