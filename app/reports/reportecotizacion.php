<?php
// reports/reportecotizacion.php

date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/app/models/sesion.php';
require_once dirname(__DIR__, 2) . '/app/config/app.php';
require_once dirname(__DIR__, 2) . '/app/helpers/helper.php';
require_once dirname(__DIR__, 2) . '/app/models/Colaborador.php';
require_once dirname(__DIR__, 2) . '/app/models/Cotizacion.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;

// — Parámetros de sesión / usuario —
$colModel = new Colaborador();
$usuario = $colModel->getById($idadmin);
$usuarioNombre = $usuario['nombreCompleto'] ?? 'Usuario';

// — ID de cotización por GET —
$idcot = $_GET['idcotizacion'] ?? null;
if (!$idcot) {
    die('Falta id de cotización');
}

// — Conexión y modelo —
$cotiModel = new Cotizacion();
$pdo = $cotiModel->getPdo();

// 1) Recuperar cabecera explícita
$info = $cotiModel->getCabeceraById((int)$idcot);
if (!$info) {
    die("No se encontró información para la cotización $idcot");
}

// 1b) Obtener fecha de creación (TIMESTAMP creado)
$stmtC = $pdo->prepare("SELECT creado FROM cotizaciones WHERE idcotizacion = ?");
$stmtC->execute([$idcot]);
$info['creado'] = $stmtC->fetchColumn();

// 2) Recuperar detalle completo
$stmt = $pdo->prepare(
    "SELECT *
     FROM vista_detalle_cotizacion_pdf
     WHERE idcotizacion = :idcotizacion"
);
$stmt->execute([':idcotizacion' => $idcot]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3) Separar productos y servicios
$productos = [];
$servicios = [];
foreach ($rows as $r) {
    if ($r['registro_tipo'] === 'producto') {
        $productos[] = [
            'descripcion' => $r['item_descripcion'],
            'cantidad'    => $r['cantidad'],
            'precio'      => $r['precio_unitario'],
            'descuento'   => $r['descuento_unitario'],
            'total'       => $r['total_linea'],
        ];
    } else {
        $servicios[] = [
            'tipo_servicio' => $r['tipo_servicio'],
            'nombre'        => $r['servicio_nombre'],
            'precio'        => $r['precio_servicio'],
        ];
    }
}

// 4) Cargar plantilla HTML
ob_start();
require __DIR__ . '/css/estilos_pdf.html';
require __DIR__ . '/content/data-reporte-cotizacion.php';
$content = ob_get_clean();

// 5) Generar PDF
$html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', [10,10,10,10]);
$html2pdf->setDefaultFont('helvetica');
$html2pdf->writeHTML($content);
if (ob_get_length()) {
    ob_end_clean();
}
$html2pdf->output("cotizacion-$idcot.pdf", 'I');
?>
