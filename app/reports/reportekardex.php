<?php
// reportekardex.php

require_once __DIR__ . '/../../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

$idproducto = $_GET['idproducto'] ?? null;
$fecha      = $_GET['fecha']      ?? date('Y-m-d');
$modo       = $_GET['modo']       ?? 'dia';
$nombre     = $_GET['nombre']     ?? '— Producto sin nombre';

if (!$idproducto) {
    die('Falta id de producto');
}

// 1) Recuperar datos de movimientos vía tu API
$apiMovUrl = sprintf(
    'http://localhost/Fix360/app/controllers/Movimiento.controller.php?idproducto=%s&modo=%s&fecha=%s',
    urlencode($idproducto),
    urlencode($modo),
    urlencode($fecha)
);  

$json = @file_get_contents($apiMovUrl);
if ($json === false) {
    die("Error al llamar a la API de movimientos");
}

$obj = json_decode($json, true);
if (!isset($obj['status']) || $obj['status'] !== 'success') {
    // Si la API devuelve status distinto, asumimos sin datos
    $datos = [];
} else {
    $datos = $obj['data'];
}

// 2) Ahora podemos capturar la plantilla
ob_start();
  // dentro de data-reporte-kardex.php tendrás acceso a $datos
  require __DIR__ . '/content/data-reporte-kardex.php';
$content = ob_get_clean();

// 3) Generar el PDF con html2pdf…
$html2pdf = new Html2Pdf('P','A4','es', true,'UTF-8',[10,10,10,10]);
$html2pdf->setDefaultFont('helvetica');
$html2pdf->writeHTML($content);
$html2pdf->output("kardex-{$idproducto}.pdf", 'I');