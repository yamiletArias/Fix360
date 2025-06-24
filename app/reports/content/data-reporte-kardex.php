<page backtop="20mm" backbottom="15mm" backleft="5mm" backright="5mm">
  <page_header>
    <h1 style="font-size:16pt; margin:0">Movimientos del día <?= date('d/m/Y', strtotime($fecha)) ?></h1>
    <h2 style="font-size:14pt; margin:0">de <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h2>
    <hr>
  </page_header>

  <page_footer>
    <hr>
    <div class="page-number">Página [[page_cu]]/[[page_nb]]</div>
  </page_footer>

  <div class="reporte-kardex">
    <div class="info-header">
      <strong>Usuario:</strong> <?= htmlspecialchars($usuarioNombre, ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Stock actual:</strong> <?= $stock_actual ?>&nbsp;&nbsp;
      <strong>Stock mínimo:</strong> <?= $stock_min ?>&nbsp;&nbsp;
      <strong>Stock máximo:</strong> <?= $stock_max ?>
    </div>

    <table border="1" cellpadding="2" cellspacing="0" width="100%" style="font-size:12pt; border-collapse:collapse;">
      <colgroup>
        <col style="width:5%;" />   <!-- # -->
        <col style="width:15%;" />  <!-- Fecha -->
        <col style="width:15%;" />  <!-- Flujo -->
        <col style="width:20%;" />  <!-- Tipo -->
        <col style="width:15%;" />  <!-- Cantidad -->
        <col style="width:15%;" />  <!-- Precio Unitario -->
        <col style="width:15%;" />  <!-- Saldo -->
      </colgroup>
      <thead>
        <tr>
          <th style="padding:6px; border:1px solid #333;">#</th>
          <th style="padding:2px 4px; border:1px solid #333; text-align:center;">Fecha</th>
          <th style="padding:2px 4px; border:1px solid #333; text-align:center;">Flujo</th>
          <th style="padding:2px 4px; border:1px solid #333; text-align:center;">Tipo</th>
          <th style="padding:2px 4px; border:1px solid #333; text-align:center;">Cantidad</th>
          <th style="padding:2px 4px; border:1px solid #333; text-align:center;">Precio U.</th>
          <th style="padding:2px 4px; border:1px solid #333; text-align:center;">Saldo</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($datos as $i => $fila): ?>
          <tr <?= $i % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
            <td style="padding:2px 4px; border:1px solid #333;"><?= $i + 1 ?></td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:center;"><?= date('d/m/Y', strtotime($fila['fecha'])) ?></td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:center;"><?= htmlspecialchars($fila['flujo'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:center;"><?= htmlspecialchars($fila['tipo_movimiento'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:right;"><?= number_format($fila['cantidad'], 2) ?></td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:right;"><?= number_format($fila['preciounit'], 2) ?></td>
            <td style="padding:2px 4px; border:1px solid #333; text-align:right;"><?= number_format($fila['saldo_restante'], 2) ?></td>
          </tr>
        <?php endforeach; ?>

        <?php if (empty($datos)): ?>
          <tr>
            <td colspan="7" style="text-align:center; padding:6px; border:1px solid #333;">
              No hay movimientos en este periodo
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</page>
