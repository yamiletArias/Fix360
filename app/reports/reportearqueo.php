<?php
// reports/reportearqueocaja.php

require_once __DIR__ . '/../../vendor/autoload.php';


use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

// Parámetros esperados: ?fecha=YYYY-MM-DD
$fecha = $_GET['fecha'] ?? date('Y-m-d');
$usuario = $_GET['usuario'] ?? null; // opcional: nombre o ID para mostrar

try {
    // 1) Recuperar datos de ingresos y egresos vía tu API de Arqueo
    $baseUrl = 'http://localhost/Fix360/app/controllers/Arqueo.controller.php';

    // Función auxiliar para fetch y decode
    function fetchJson($url)
    {
        $json = @file_get_contents($url);
        if (!$json)
            return [];
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    $urlIngresos = sprintf(
        "%s?accion=ingresos&fecha=%s",
        $baseUrl,
        urlencode($fecha)
    );
    $ingresos = fetchJson($urlIngresos);

    $urlEgresos = sprintf(
        "%s?accion=egresos&fecha=%s",
        $baseUrl,
        urlencode($fecha)
    );
    $egresos = fetchJson($urlEgresos);

    $urlResumen = sprintf(
        "%s?accion=resumen&fecha=%s",
        $baseUrl,
        urlencode($fecha)
    );
    $resumen = fetchJson($urlResumen);

    // 2) Cargar plantilla HTML dinámico
    ob_start();
    require __DIR__ . '/content/data-reporte-caja.php';
    $html = ob_get_clean();

    // 3) Generar y enviar PDF
    $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', [10, 15, 10, 5]);
    $html2pdf->setDefaultFont('helvetica');
    $html2pdf->writeHTML($html);
    $html2pdf->output("arqueo-caja-{$fecha}.pdf", 'I');
    exit;
} catch (Html2PdfException $e) {
    if (isset($html2pdf)) {
        $html2pdf->clean();
    }
    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
    exit;
}