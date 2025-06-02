<?php
// reportekardex.php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/app/models/sesion.php';

require_once dirname(__DIR__, 2) . '/app/config/app.php';
require_once dirname(__DIR__, 2) . '/app/helpers/helper.php';
require_once dirname(__DIR__, 2) . '/app/models/Colaborador.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

// Parámetros básicos
$colModel      = new Colaborador();
$usuario       = $colModel->getById($idadmin);

// Concatenamos directamente apellidos + nombres:
$usuarioNombre = trim("{$usuario['apellidos']} {$usuario['nombres']}");

$idproducto = $_GET['idproducto'] ?? null;
$fecha      = $_GET['fecha']      ?? date('Y-m-d');
$modo       = $_GET['modo']       ?? 'dia';
$nombre     = $_GET['nombre']     ?? '— Producto sin nombre';

if (!$idproducto) {
    die('Falta id de producto');
}

// 2) Recuperar movimientos
$apiMovUrl = sprintf(
    'http://localhost/Fix360/app/controllers/Movimiento.controller.php?idproducto=%s&modo=%s&fecha=%s',
    urlencode($idproducto),
    urlencode($modo),
    urlencode($fecha)
);
$jsonMov = @file_get_contents($apiMovUrl);
$objMov  = $jsonMov ? json_decode($jsonMov, true) : null;
$datos   = ($objMov && isset($objMov['status']) && $objMov['status'] === 'success')
           ? $objMov['data']
           : [];

// 3) Recuperar stock via API de Kardex.Controller.php
$apiStockUrl = sprintf(
    'http://localhost/Fix360/app/controllers/Kardex.Controller.php?task=getStock&idproducto=%s',
    urlencode($idproducto)
);
$jsonStock = @file_get_contents($apiStockUrl);
$objStock  = $jsonStock ? json_decode($jsonStock, true) : [];
$stock_actual = $objStock['stock_actual'] ?? '';
$stock_min    = $objStock['stockmin']      ?? '';
$stock_max    = $objStock['stockmax']      ?? '';

// (YA NO volver a asignar $usuarioNombre aquí; la dejamos con apellidos+nombres)

// 5) Capturar plantilla
ob_start();

// variables disponibles en la plantilla:
//   $datos, $stock_actual, $stock_min, $stock_max, $usuarioNombre, $nombre, $fecha, $modo
require __DIR__ . '/css/estilos_pdf.html';
require __DIR__ . '/content/data-reporte-kardex.php';
$content = ob_get_clean();

// 6) Generar PDF
$html2pdf = new Html2Pdf('P','A4','es', true,'UTF-8',[10,10,10,10]);
$html2pdf->setDefaultFont('helvetica');
$html2pdf->writeHTML($content);
if (ob_get_length()) {
    ob_end_clean();
}
$html2pdf->output("kardex-{$idproducto}.pdf", 'I');
