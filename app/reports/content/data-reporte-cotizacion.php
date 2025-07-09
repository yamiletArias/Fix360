<?php
// Calcular totales
$total_productos = 0;
$total_servicios = 0;

// Sumar productos
foreach ($productos as $p) {
  $total_productos += $p['total'];
}

// Sumar servicios
foreach ($servicios as $s) {
  $total_servicios += $s['precio'];
}

$total_general = $total_productos + $total_servicios;
?>
<page backtop="20mm" backbottom="15mm" backleft="5mm" backright="5mm">
  <page_header>
    <div style="text-align:center; margin:0; padding:0;">
      <img src="<?php echo realpath(__DIR__ . '/../../../images/headert.png'); ?>"
        style="width:100%; height:auto; display:block;" alt="Header">
    </div>
  </page_header>

  <page_footer>
    <div class="footer" style="text-align:center; margin:4px 0;">
      <img class="img-footer" src="<?php echo realpath(__DIR__ . '/../../../images/footer.png'); ?>"
        style="max-width:100%; height:auto; display:block; margin:0 auto;" alt="Footer">
    </div>
    <div class="page-number" style="text-align:right; font-size:10pt; margin-top:2mm;">
      Página [[page_cu]]/[[page_nb]]
    </div>
  </page_footer>

  <div style="margin-top:60px; font-size:12pt;">
    <h1 style="font-size:16pt; margin:5px; text-align:right;">
      Cotización #<?= htmlspecialchars($info['idcotizacion'], ENT_QUOTES, 'UTF-8') ?> —
      <?= date('d/m/Y H:i', strtotime($info['creado'])) ?>
    </h1>
    <strong>Propietario:</strong> <?= htmlspecialchars($info['cliente'], ENT_QUOTES, 'UTF-8') ?><br>
    <strong>Vigencia:</strong> <?= (int) $info['vigenciadias'] ?> días<br>
    <?php if (!empty($info['justificacion'])): ?>
      <strong>Justificación:</strong> <?= htmlspecialchars($info['justificacion'], ENT_QUOTES, 'UTF-8') ?><br>
    <?php endif; ?>
  </div>

  <h3 style="margin-top:15px;">Detalle de Productos</h3>
  <table border="1" cellpadding="4" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11pt;">
    <colgroup>
      <col style="width:5%">
      <col style="width:55%">
      <col style="width:10%">
      <col style="width:10%">
      <col style="width:10%">
      <col style="width:10%">
    </colgroup>
    <thead>
      <tr>
        <th>#</th>
        <th>Producto</th>
        <th>Cant.</th>
        <th>Precio</th>
        <th>Dscto</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($productos)): ?>
        <?php foreach ($productos as $i => $p): ?>
          <tr <?= $i % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
            <td align="center"><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($p['descripcion'], ENT_QUOTES, 'UTF-8') ?></td>
            <td align="center"><?= $p['cantidad'] ?></td>
            <td align="right"><?= number_format($p['precio'], 2) ?></td>
            <td align="right"><?= number_format($p['descuento'], 2) ?></td>
            <td align="right"><?= number_format($p['total'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" align="center">No hay productos registrados.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php if (count($servicios)): ?>
    <h3 style="margin-top:15px;">Detalle de Servicios</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11pt;">
      <colgroup>
        <col style="width:5%">
        <col style="width:45%">
        <col style="width:25%">
        <col style="width:25%">
      </colgroup>
      <thead>
        <tr>
          <th>#</th>
          <th>Tipo de Servicio</th>
          <th>Nombre</th>
          <th>Precio</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($servicios as $i => $s): ?>
          <tr <?= $i % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
            <td align="center"><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($s['tipo_servicio'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($s['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td align="right"><?= number_format($s['precio'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
  <!-- TOTAL GENERAL alineado a la derecha -->
  <div style="
    width:100%;
    margin-top:12px;
    padding-top:4px;
    font-size:12pt;
    font-family:sans-serif;
    font-weight:bold;
    text-align:right;
  ">
    TOTAL S/. <?= number_format($total_general, 2) ?>
  </div>

</page>