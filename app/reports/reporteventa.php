<?php
// reports/reporteventa.php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/app/models/sesion.php';
require_once dirname(__DIR__, 2) . '/app/config/app.php';
require_once dirname(__DIR__, 2) . '/app/helpers/helper.php';
require_once dirname(__DIR__, 2) . '/app/models/Colaborador.php';
require_once dirname(__DIR__, 2) . '/app/models/Venta.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;

// Parámetros básicos
$colModel = new Colaborador();
$usuario = $colModel->getById($idadmin);
$usuarioNombre = $usuario['nombreCompleto'];

$idventa = $_GET['idventa'] ?? null;
if (!$idventa) {
    die('Falta id de venta');
}

// Conexión directa a la base de datos
$ventaModel = new Venta();
$pdo = $ventaModel->getPdo();

// 1) Consulta la vista que da header, productos y servicios
$stmt = $pdo->prepare(
    "SELECT * FROM vista_detalle_venta_pdf WHERE idventa = :idventa"
);
$stmt->execute([':idventa' => $idventa]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si no hay datos, abortar
if (empty($rows)) {
    die('No se encontró información para la venta ' . htmlspecialchars($idventa));
}

// Cabecera: toma datos de la primera fila
$first = $rows[0];
$info = [
    'idventa'           => $idventa,
    'fechahora'         => $first['fecha'] ?? date('Y-m-d H:i:s'),
    'cliente'           => $first['cliente'] ?? 'Sin cliente',
    'vehiculo'          => $first['vehiculo'] ?? 'Sin vehículo',
    'kilometraje'       => $first['kilometraje'] ?? 'Sin kilometraje',
    'tipo_comprobante'  => $first['tipocom'] ?? 'Boleta',
    'numero_comprobante'=> $first['numcomp'] ?? 'Sin número',
    'propietario'       => $first['propietario'] ?? 'Sin propietario'
];

// 2) Detalle de productos y 3) Servicios
$productos = [];
$servicios = [];

foreach ($rows as $row) {
    // Agrega producto de cada fila
    $productos[] = [
        'producto'       => $row['producto'],
        'cantidad'       => $row['cantidad'],
        'precio'         => $row['precio'],
        'descuento'      => $row['descuento'],
        'total_producto' => $row['total_producto'],
    ];
    
    // Si existe un servicio en esta fila
    if (!empty($row['nombreservicio'])) {
        $servicios[] = [
            'tiposervicio'    => $row['tiposervicio'],
            'nombreservicio'  => $row['nombreservicio'],
            'mecanico'        => $row['mecanico'],
            'precio_servicio' => $row['precio_servicio'],
        ];
    }
}

// Variables adicionales
$tipoComprobante   = $info['tipo_comprobante'];
$numeroComprobante = $info['numero_comprobante'];
$propietario       = $info['propietario'];
$cliente           = $info['cliente'];
$fechaVenta        = date('d/m/Y H:i', strtotime($info['fechahora']));

// 4) Capturar plantilla
ob_start();
require __DIR__ . '/css/estilos_pdf.html';
require __DIR__ . '/content/data-reporte-venta.php';
$content = ob_get_clean();

// 5) Generar PDF
$html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', [10, 10, 10, 10]);
$html2pdf->setDefaultFont('helvetica');
$html2pdf->writeHTML($content);
if (ob_get_length()) {
    ob_end_clean();
}
$html2pdf->output("venta-{$idventa}.pdf", 'I');
?>
