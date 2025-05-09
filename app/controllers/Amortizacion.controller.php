<?php
ini_set('display_errors',0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
if (ob_get_level()) ob_end_clean();

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set("America/Lima");

require_once __DIR__ . '/../models/Amortizacion.php';
$am = new Amortizacion();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idventa = isset($_GET['idventa']) ? (int)$_GET['idventa'] : 0;
    if ($idventa <= 0) {
        http_response_code(400);
        echo json_encode(['status'=>'error','error'=>'Falta idventa']);
        exit;
    }
    $data  = $am->listByVenta($idventa);
    $total = $am->getTotalPorVenta($idventa);
    echo json_encode(
      [
      'status'  =>  'success',
      'data'    =>  $data,
      'total'   =>  $total
    ]);
    exit;
    
}

http_response_code(405);
echo json_encode(['status'=>'error','message'=>'Método no permitido']);
exit;
