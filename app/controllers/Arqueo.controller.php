<?php
// C:\xampp\htdocs\Fix360\app\controllers\Arqueo.controller.php

// Conexión al modelo
require_once __DIR__ . '/../models/Arqueo.php';

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Lima');

// Limpia buffers de salida para devolver solo JSON
while (ob_get_level())
  ob_end_clean();

$fecha = $_GET['fecha'] ?? date('Y-m-d');

// Si piden ingresos (o por defecto)
if (!isset($_GET['accion']) || $_GET['accion'] === 'ingresos') {
  $arqueo = new Arqueo();
  $ingresos = $arqueo->getIngresosPorFecha($fecha);
  echo json_encode($ingresos, JSON_UNESCAPED_UNICODE);
  exit;
}
// Si piden egresos
if ($_GET['accion'] === 'egresos') {
  $arqueo = new Arqueo();
  $egresos = $arqueo->getEgresosPorFecha($fecha);
  echo json_encode($egresos, JSON_UNESCAPED_UNICODE);
  exit;
}

// Si piden el resumen
if ($_GET['accion'] === 'resumen') {
  $arqueo = new Arqueo();
  $resumen = $arqueo->getResumenPorFecha($fecha);
  echo json_encode($resumen, JSON_UNESCAPED_UNICODE);
  exit;
}

// Aquí podrías manejar otras acciones (egresos, resumen, etc.)
