<page backtop="20mm" backbottom="15mm" backleft="5mm" backright="5mm">
  <page_header>
    <h1 style="font-size:16pt; margin:0">
      Tipo: <?= htmlspecialchars($tipoComprobante) ?> | Núm: <?= htmlspecialchars($numeroComprobante) ?>
    </h1>
    <h2 style="font-size:14pt; margin:0">
      Propietario: <?= htmlspecialchars($propietario, ENT_QUOTES, 'UTF-8') ?>
    </h2>
    <h2 style="font-size:14pt; margin:0">
      Cliente: <?= htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8') ?>
    </h2>
    <h2 style="font-size:12pt; margin:0">Fecha: <?= $fechaVenta ?></h2>
    <hr>
  </page_header>
  <page_footer>
    <hr>
    <div class="page-number">Página [[page_cu]]/[[page_nb]]</div>
  </page_footer>

  <div class="reporte-venta">
    <div class="info-header" style="margin-bottom:10px;">
      <strong>Vehículo:</strong> <?= htmlspecialchars($objInfo['vehiculo'] ?? '—', ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Kilometraje:</strong> <?= htmlspecialchars($objInfo['kilometraje'] ?? '—', ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Usuario:</strong> <?= htmlspecialchars($usuarioNombre, ENT_QUOTES, 'UTF-8') ?><br>
    </div>

    <!-- Tabla Productos -->
    <table border="1" cellpadding="2" cellspacing="0" width="100%" style="font-size:12pt; border-collapse:collapse;">
      <thead>
        <tr>
          <th>#</th>
          <th>Producto</th>
          <th>Cant.</th>
          <th>Precio</th>
          <th>Desc.</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($productos as $i => $p): ?>
          <tr <?= $i % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($p['producto'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="text-align:center"><?= $p['cantidad'] ?></td>
            <td style="text-align:right"><?= $p['precio'] ?></td>
            <td style="text-align:right"><?= $p['descuento'] ?></td>
            <td style="text-align:right"><?= $p['total_producto'] ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($productos)): ?>
          <tr><td colspan="6" style="text-align:center;">No hay productos</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Tabla Servicios -->
    <?php if (!empty($servicios)): ?>
      <h3>Servicios</h3>
      <table border="1" cellpadding="2" cellspacing="0" width="100%" style="font-size:12pt; border-collapse:collapse; margin-top:5px;">
        <thead>
          <tr>
            <th>#</th>
            <th>Tipo</th>
            <th>Servicio</th>
            <th>Mecánico</th>
            <th>Precio</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($servicios as $j => $s): ?>
            <tr <?= $j % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
              <td><?= $j + 1 ?></td>
              <td><?= htmlspecialchars($s['tiposervicio'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($s['nombreservicio'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($s['mecanico'], ENT_QUOTES, 'UTF-8') ?></td>
              <td style="text-align:right"><?= $s['precio_servicio'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

  </div>
</page>
