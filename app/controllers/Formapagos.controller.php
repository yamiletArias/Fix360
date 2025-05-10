<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../models/Conexion.php';

try {
  $db = Conexion::getConexion();
  $fps = $db->query("SELECT idformapago, formapago FROM formapagos")->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['status'=>'success','data'=>$fps]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['status'=>'error','message'=>'Error al obtener formas de pago']);
}
