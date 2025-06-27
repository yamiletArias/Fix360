<page backtop="20mm" backbottom="15mm" backleft="5mm" backright="5mm">

  <page_header>
    <!-- Logo centrado -->
    <div style="text-align:center; margin:0; padding:0;">
      <img src="<?php echo realpath(__DIR__ . '/../../../images/headert.png'); ?>"
        style="width:100%; height:auto; display:block;" alt="Header">
    </div>

    <!-- Línea flex principal -->
    <div style="
          display:flex;
          justify-content:space-between;
          align-items:flex-start;
          margin-top:10mm;
        ">
      <!-- Izquierda: Propietario -->
      <div>
        <h2 style="font-size:12pt; margin:4px 0 0 0;">
          Propietario: <?= htmlspecialchars($info['propietario'], ENT_QUOTES, 'UTF-8') ?>
        </h2>
      </div>

      <!-- Derecha: Fecha y Comprobante -->
      <div style="text-align:right;">
        <h1 style="font-size:12pt; margin:0;">
          <?= date('d/m/Y H:i', strtotime($info['fechahora'])) ?>
        </h1>
        <h3 style="font-size:12pt; margin:4px 0 0 0;">
          <?= htmlspecialchars($info['tipo_comprobante'], ENT_QUOTES, 'UTF-8') ?>
          N° <?= htmlspecialchars($info['numero_comprobante'], ENT_QUOTES, 'UTF-8') ?>
        </h3>
      </div>
    </div>
  </page_header>

  <page_footer>
    <!-- Footer con imagen -->
    <div class="footer" style="text-align:center; margin:4px 0;">
      <img class="img-footer" src="<?php echo realpath(__DIR__ . '/../../../images/footer.png'); ?>"
        style="max-width:100%; height:auto; display:block; margin:0 auto;" alt="Footer">
    </div>
    <div class="page-number" style="text-align:right; font-size:10pt; margin-top:2mm;">
      Página [[page_cu]]/[[page_nb]]
    </div>
  </page_footer>

  <div class="reporte-venta">
    <!-- Info Cliente -->
    <div class="info-header" style="margin-top:150px; font-size:12pt;">
      <strong>Cliente:</strong> <?= htmlspecialchars($info['cliente'], ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Vehículo:</strong> <?= htmlspecialchars($info['vehiculo'], ENT_QUOTES, 'UTF-8') ?> |
      <strong>Kilometraje:</strong> <?= htmlspecialchars($info['kilometraje'], ENT_QUOTES, 'UTF-8') ?><br>
    </div>

    <!-- Productos -->
    <h3 style="margin-top:10mm;">Productos</h3>
    <table border="1" cellpadding="2" cellspacing="0" width="100%" style="font-size:12pt; border-collapse:collapse;">
      <colgroup>
        <col style="width:5%;" />
        <col style="width:50%;" />
        <col style="width:10%;" />
        <col style="width:10%;" />
        <col style="width:15%;" />
        <col style="width:10%;" />
      </colgroup>
      <thead>
        <tr>
          <th style="padding:6px; border:1px solid #333;">#</th>
          <th style="padding:2px 4px; border:1px solid #333;text-align:left;">Producto</th>
          <th style="padding:2px 4px; border:1px solid #333;text-align:center;">Cant.</th>
          <th style="padding:2px 4px; border:1px solid #333;text-align:right;">Precio</th>
          <th style="padding:2px 4px; border:1px solid #333;text-align:right;">Desc.</th>
          <th style="padding:2px 4px; border:1px solid #333;text-align:right;">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($productos as $i => $p): ?>
          <tr <?= $i % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
            <td style="padding:2px 4px; border:1px solid #333;"><?= $i + 1 ?></td>
            <td style="padding:2px 4px; border:1px solid #333;">
              <?= htmlspecialchars($p['producto'], ENT_QUOTES, 'UTF-8') ?>
            </td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:center;"><?= $p['cantidad'] ?></td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:right;"><?= number_format($p['precio'], 2) ?>
            </td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:right;"><?= number_format($p['descuento'], 2) ?>
            </td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:right;">
              <?= number_format($p['total_producto'], 2) ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($productos)): ?>
          <tr>
            <td colspan="6" style="text-align:center; padding:6px; border:1px solid #333;">No hay productos</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Servicios -->
    <?php if (!empty($servicios)): ?>
      <h3 style="margin-top:8mm;">Servicios</h3>
      <table border="1" cellpadding="2" cellspacing="0" width="100%" style="font-size:12pt; border-collapse:collapse;">
        <colgroup>
          <col style="width:5%;" />
          <col style="width:25%;" />
          <col style="width:20%;" />
          <col style="width:30%;" />
          <col style="width:20%;" />
        </colgroup>
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
                <?= htmlspecialchars($s['tiposervicio'], ENT_QUOTES, 'UTF-8') ?>
              </td>
              <td style="padding:2px 4px; border:1px solid #333;">
                <?= htmlspecialchars($s['nombreservicio'], ENT_QUOTES, 'UTF-8') ?>
              </td>
              <td style="padding:2px 4px; border:1px solid #333;">
                <?= htmlspecialchars($s['mecanico'], ENT_QUOTES, 'UTF-8') ?>
              </td>
              <td style="padding:2px 4px; border:1px solid #333; text-align:right;">
                <?= number_format($s['precio_servicio'], 2) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- Resumen de Totales -->
    <?php
    // Tus cálculos de totales
    $totalProductos = array_sum(array_column($productos, 'total_producto'));
    $totalServicios = !empty($servicios)
      ? array_sum(array_column($servicios, 'precio_servicio'))
      : 0;
    $granTotal = $totalProductos + $totalServicios;
    ?>
    <!-- TOTAL GENERAL alineado a la derecha -->
    <div style="
      width:100%;
      margin-top:12px;           /* ahora más separación */
      border-top:1px solid #333;
      padding-top:4px;
      font-size:12pt;
      font-family:sans-serif;
      font-weight:bold;
      text-align:right;
    ">
      TOTAL S/. <?= number_format($granTotal, 2) ?>
    </div>

  </div>
</page>