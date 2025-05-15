<?php
// app/auth.php
session_start();

// 1) Validar login
if (
    ! isset($_SESSION['login']['status']) ||
    $_SESSION['login']['status'] !== true
) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
      'status'  => 'error',
      'message' => 'No autorizado'
    ]);
    exit;
}

// 2) Defino la variable que usan los controllers
$idadmin = $_SESSION['login']['idcolaborador'];
