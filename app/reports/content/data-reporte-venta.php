<!-- reports/content/data-reporte-venta.php -->
<page backtop="20mm" backbottom="15mm" backleft="5mm" backright="5mm">
  <page_header>
    <h1 style="font-size:16pt; margin:0">
      Detalle de Venta <?= date('d/m/Y H:i', strtotime($info['fechahora'])) ?>
    </h1>
    <h2 style="font-size:14pt; margin:0">
      Cliente: <?= htmlspecialchars($info['cliente'], ENT_QUOTES, 'UTF-8') ?>
    </h2>
    <!-- NUEVO bloque -->
    <h3 style="font-size:12pt; margin:0; margin-top:4px;">
      Comprobante: <?= htmlspecialchars($info['tipo_comprobante'], ENT_QUOTES, 'UTF-8') ?>
      &nbsp;&nbsp;N° <?= htmlspecialchars($info['numero_comprobante'], ENT_QUOTES, 'UTF-8') ?>
    </h3>
    <hr>
  </page_header>

  <page_footer>
    <hr>
    <div class="page-number">Página [[page_cu]]/[[page_nb]]</div>
  </page_footer>

  <div class="reporte-venta">
    <div class="info-header">
      <strong>Cliente:</strong> <?= htmlspecialchars($info['cliente'], ENT_QUOTES, 'UTF-8') ?><br>
      <!-- <strong>ID Venta:</strong> <?= $info['idventa'] ?>&nbsp;&nbsp; -->
      <strong>Vehículo:</strong> <?= htmlspecialchars($info['vehiculo'], ENT_QUOTES, 'UTF-8') ?>,&nbsp;
      <strong>Kilometraje:</strong> <?= htmlspecialchars($info['kilometraje'], ENT_QUOTES, 'UTF-8') ?><br>
    </div>

    <h3>Productos</h3>
    <table border="1" cellpadding="2" cellspacing="0" width="100%" style="font-size:12pt; border-collapse:collapse;">
      <thead>
        <tr>
          <th style="padding:6px; border:1px solid #333;">#</th>
          <th style="padding:2px 4px; border:1px solid #333;text-align:left;">Producto</th>
          <th style="padding:2px 4px; border:1px solid #333;text-align:center;">Cantidad</th>
          <th style="padding:2px 4px; border:1px solid #333;text-align:right;">Precio</th>
          <th style="padding:2px 4px; border:1px solid #333;text-align:right;">Descuento</th>
          <th style="padding:2px 4px; border:1px solid #333;text-align:right;">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($productos as $i => $p): ?>
          <tr <?= $i % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
            <td style="padding:2px 4px; border:1px solid #333;"><?= $i + 1 ?></td>
            <td style="padding:2px 4px; border:1px solid #333;">
              <?= htmlspecialchars($p['producto'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:center;"><?= $p['cantidad'] ?></td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:right;"><?= number_format($p['precio'], 2) ?>
            </td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:right;"><?= number_format($p['descuento'], 2) ?>
            </td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:right;">
              <?= number_format($p['total_producto'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($productos)): ?>
          <tr>
            <td colspan="6" style="text-align:center; padding:6px; border:1px solid #333;">No hay productos</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <?php if (!empty($servicios)): ?>
      <h3>Servicios</h3>
      <table border="1" cellpadding="2" cellspacing="0" width="100%" style="font-size:12pt; border-collapse:collapse;">
        <thead>
          <tr>
            <th style="padding:6px; border:1px solid #333;">#</th>
            <th style="padding:2px 4px; border:1px solid #333;text-align:left;">Tipo</th>
            <th style="padding:2px 4px; border:1px solid #333;text-align:left;">Servicio</th>
            <th style="padding:2px 4px; border:1px solid #333;text-align:left;">Mecánico</th>
            <th style="padding:2px 4px; border:1px solid #333;text-align:right;">Precio</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($servicios as $j => $s): ?>
            <tr <?= $j % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
              <td style="padding:2px 4px; border:1px solid #333;"><?= $j + 1 ?></td>
              <td style="padding:2px 4px; border:1px solid #333;">
                <?= htmlspecialchars($s['tiposervicio'], ENT_QUOTES, 'UTF-8') ?></td>
              <td style="padding:2px 4px; border:1px solid #333;">
                <?= htmlspecialchars($s['nombreservicio'], ENT_QUOTES, 'UTF-8') ?></td>
              <td style="padding:2px 4px; border:1px solid #333;">
                <?= htmlspecialchars($s['mecanico'], ENT_QUOTES, 'UTF-8') ?></td>
              <td style="padding:2px 4px; border:1px solid #333; text-align:right;">
                <?= number_format($s['precio_servicio'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</page>