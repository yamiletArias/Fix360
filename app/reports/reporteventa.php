<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/app/models/sesion.php';
require_once dirname(__DIR__, 2) . '/app/config/app.php';
require_once dirname(__DIR__, 2) . '/app/helpers/helper.php';
require_once dirname(__DIR__, 2) . '/app/models/Colaborador.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;

// Parámetros básicos
$colModel = new Colaborador();
$usuario = $colModel->getById($idadmin);
$usuarioNombre = $usuario['nombreCompleto'];
$idventa = $_GET['idventa'] ?? null;
if (!$idventa)
  die('Falta id de venta');

// Consultar directamente la vista para obtener todos los datos
$apiUrl = sprintf(
  '%sapp/controllers/Venta.controller.php?action=detalle_completo&idventa=%s',
  SERVERURL,
  urlencode($idventa)
);
$jsonData = @file_get_contents($apiUrl);
$datosCompletos = $jsonData ? json_decode($jsonData, true) : [];

// Obtener información general de la primera fila (es la misma para toda la venta)
$infoGeneral = !empty($datosCompletos) ? $datosCompletos[0] : [];

// Separar productos y servicios
$productos = [];
$servicios = [];

foreach ($datosCompletos as $fila) {
  // Agregar producto si tiene datos
  if (!empty($fila['producto']) && !empty($fila['cantidad'])) {
    $productos[] = [
      'producto' => $fila['producto'],
      'cantidad' => $fila['cantidad'],
      'precio' => $fila['precio'],
      'descuento' => $fila['descuento'],
      'total_producto' => $fila['total_producto']
    ];
  }

  // Agregar servicio si tiene datos (evitar duplicados)
  if (!empty($fila['nombreservicio']) && !empty($fila['precio_servicio'])) {
    $servicioKey = $fila['tiposervicio'] . '|' . $fila['nombreservicio'] . '|' . $fila['mecanico'];
    if (!isset($servicios[$servicioKey])) {
      $servicios[$servicioKey] = [
        'tiposervicio' => $fila['tiposervicio'],
        'nombreservicio' => $fila['nombreservicio'],
        'mecanico' => $fila['mecanico'],
        'precio_servicio' => $fila['precio_servicio']
      ];
    }
  }
}

// Convertir servicios de array asociativo a numérico
$servicios = array_values($servicios);

// Asignación de campos generales
$tipoComprobante = $infoGeneral['tipo_comprobante'] ?? '—';
$numeroComprobante = $infoGeneral['numero_comprobante'] ?? '—';
$propietario = $infoGeneral['propietario'] ?? '—';
$cliente = $infoGeneral['cliente'] ?? '—';
$vehiculo = $infoGeneral['vehiculo'] ?? '—';
$kilometraje = $infoGeneral['kilometraje'] ?? '—';
$fechaVenta = isset($infoGeneral['fecha'])
  ? date('d/m/Y H:i', strtotime($infoGeneral['fecha']))
  : date('d/m/Y H:i');

// Calcular totales
$totalProductos = array_sum(array_column($productos, 'total_producto'));
$totalServicios = array_sum(array_column($servicios, 'precio_servicio'));
$totalGeneral = $totalProductos + $totalServicios;

// Capturar plantilla
ob_start();
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12pt;
    }

    .header-info {
      margin-bottom: 15px;
    }

    .info-row {
      margin-bottom: 5px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }

    th,
    td {
      border: 1px solid #333;
      padding: 5px;
      text-align: left;
    }

    th {
      background-color: #f0f0f0;
      font-weight: bold;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    .zebra {
      background-color: #f9f9f9;
    }

    .section-title {
      font-size: 14pt;
      font-weight: bold;
      margin: 15px 0 10px 0;
    }

    .total-row {
      font-weight: bold;
      background-color: #e0e0e0;
    }
  </style>
</head>

<body>

  <page backtop="20mm" backbottom="15mm" backleft="5mm" backright="5mm">
    <page_header>
      <h1 style="font-size:16pt; margin:0">
        Tipo: <?= htmlspecialchars($tipoComprobante) ?> | Núm: <?= htmlspecialchars($numeroComprobante) ?>
      </h1>
      <hr>
    </page_header>

    <page_footer>
      <hr>
      <div class="page-number">Página [[page_cu]]/[[page_nb]]</div>
    </page_footer>

    <div class="reporte-venta">
      <!-- Información General -->
      <div class="header-info">
        <div class="info-row"><strong>Propietario:</strong> <?= htmlspecialchars($propietario, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div class="info-row"><strong>Cliente:</strong> <?= htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="info-row"><strong>Fecha:</strong> <?= $fechaVenta ?></div>
        <div class="info-row"><strong>Vehículo:</strong> <?= htmlspecialchars($vehiculo, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="info-row"><strong>Kilometraje:</strong> <?= htmlspecialchars($kilometraje, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div class="info-row"><strong>Usuario:</strong> <?= htmlspecialchars($usuarioNombre, ENT_QUOTES, 'UTF-8') ?>
        </div>
      </div>

      <!-- Tabla de Productos -->
      <div class="section-title">PRODUCTOS</div>
      <table>
        <thead>
          <tr>
            <th style="width: 5%">#</th>
            <th style="width: 45%">Producto</th>
            <th style="width: 10%">Cant.</th>
            <th style="width: 15%">Precio</th>
            <th style="width: 10%">Desc.</th>
            <th style="width: 15%">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $i => $p): ?>
              <tr <?= $i % 2 ? 'class="zebra"' : '' ?>>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($p['producto'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-center"><?= number_format($p['cantidad'], 0) ?></td>
                <td class="text-right">S/ <?= number_format($p['precio'], 2) ?></td>
                <td class="text-right">S/ <?= number_format($p['descuento'], 2) ?></td>
                <td class="text-right">S/ <?= number_format($p['total_producto'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
            <tr class="total-row">
              <td colspan="5" class="text-right">SUBTOTAL PRODUCTOS:</td>
              <td class="text-right">S/ <?= number_format($totalProductos, 2) ?></td>
            </tr>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center">No hay productos registrados</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Tabla de Servicios -->
      <?php if (!empty($servicios)): ?>
        <div class="section-title">SERVICIOS</div>
        <table>
          <thead>
            <tr>
              <th style="width: 5%">#</th>
              <th style="width: 25%">Tipo Servicio</th>
              <th style="width: 35%">Servicio</th>
              <th style="width: 20%">Mecánico</th>
              <th style="width: 15%">Precio</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($servicios as $j => $s): ?>
              <tr <?= $j % 2 ? 'class="zebra"' : '' ?>>
                <td class="text-center"><?= $j + 1 ?></td>
                <td><?= htmlspecialchars($s['tiposervicio'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($s['nombreservicio'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($s['mecanico'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-right">S/ <?= number_format($s['precio_servicio'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
            <tr class="total-row">
              <td colspan="4" class="text-right">SUBTOTAL SERVICIOS:</td>
              <td class="text-right">S/ <?= number_format($totalServicios, 2) ?></td>
            </tr>
          </tbody>
        </table>
      <?php endif; ?>

      <!-- Total General -->
      <table style="margin-top: 20px;">
        <tr class="total-row" style="font-size: 14pt;">
          <td style="width: 80%; text-align: right; padding: 10px;">TOTAL GENERAL:</td>
          <td style="width: 20%; text-align: right; padding: 10px;">S/ <?= number_format($totalGeneral, 2) ?></td>
        </tr>
      </table>

      <!-- Información adicional -->
      <div style="margin-top: 30px; font-size: 10pt; color: #666;">
        <p>Reporte generado el <?= date('d/m/Y H:i:s') ?> por
          <?= htmlspecialchars($usuarioNombre, ENT_QUOTES, 'UTF-8') ?></p>
      </div>
    </div>
  </page>

</body>

</html>

<?php
$content = ob_get_clean();

// Generar PDF
try {
  $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', [10, 10, 10, 10]);
  $html2pdf->setDefaultFont('helvetica');
  $html2pdf->writeHTML($content);

  if (ob_get_length())
    ob_end_clean();
  $html2pdf->output("venta-{$idventa}.pdf", 'I');
} catch (Html2PdfException $e) {
  die('Error al generar PDF: ' . $e->getMessage());
}
?>