<?php
// app/reports/reporteegresos.php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/app/models/sesion.php';         // valida sesión y deja $idadmin
require_once dirname(__DIR__, 2) . '/app/config/app.php';
require_once dirname(__DIR__, 2) . '/app/helpers/helper.php';
require_once dirname(__DIR__, 2) . '/app/models/Colaborador.php';
require_once dirname(__DIR__, 2) . '/app/models/Egreso.php';         // <-- nuevo include

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

// 1) Obtenemos datos del colaborador que genera el PDF
$colModel      = new Colaborador();
$usuario       = $colModel->getById($idadmin);
$usuarioNombre = trim("{$usuario['apellidos']} {$usuario['nombres']}");

// 2) Parámetros de filtro: modo, fecha, estado (tal como la tabla HTML los envía)
$modo   = $_GET['modo']   ?? 'dia';            // 'dia' | 'semana' | 'mes'
$fecha  = $_GET['fecha']  ?? date('Y-m-d');    // 'YYYY-MM-DD'
$estado = $_GET['estado'] ?? 'A';              // 'A' = Activos, 'D' = Eliminados

// 3) Invocar el modelo Egreso directamente en lugar de consumir la API vía HTTP
$egresoModel = new Egreso();
$datos = $egresoModel->listarPorPeriodo([
    'modo'   => $modo,
    'fecha'  => $fecha,
    'estado' => $estado
]);

// 4) Capturar la plantilla
ob_start();

// variables disponibles en la plantilla:
//   $datos, $usuarioNombre, $modo, $fecha, $estado
require __DIR__ . '/css/estilos_pdf_egresos.html';
require __DIR__ . '/content/data-reporte-egresos.php';

$content = ob_get_clean();

// 5) Generar PDF con Html2Pdf
try {
  $html2pdf = new Html2Pdf('L', 'A4', 'es', true, 'UTF-8', [5, 5, 15, 5]);
  $html2pdf->setDefaultFont('helvetica');
  $html2pdf->writeHTML($content);
  if (ob_get_length()) {
    ob_end_clean();
  }
  $nombrePdf = sprintf("egresos-%s-%s.pdf", $modo, date('Ymd_His'));
  $html2pdf->output($nombrePdf, 'I');
} catch (Html2PdfException $e) {
  $formatter = new ExceptionFormatter($e);
  echo $formatter->getHtmlMessage();
  exit;
}
