<!-- content/data-reporte-cotizacion.php -->
<page backtop="20mm" backbottom="15mm" backleft="5mm" backright="5mm">
  <page_header>
    <h1 style="font-size:16pt; margin:0">
      Cotización #<?= $info['idcotizacion'] ?> — <?= date('d/m/Y H:i', strtotime($info['fechahora'])) ?>
    </h1>
    <hr>
  </page_header>

  <page_footer>
    <hr>
    <div class="page-number">Página [[page_cu]]/[[page_nb]]</div>
  </page_footer>

  <div style="margin-top:10px; font-size:12pt;">
    <strong>Cliente:</strong> <?= htmlspecialchars($info['cliente'], ENT_QUOTES,'UTF-8') ?><br>
    <strong>Vigencia:</strong> <?= (int)$info['vigenciadias'] ?> días<br>
    <strong>Estado:</strong> <?= htmlspecialchars($info['estado'], ENT_QUOTES,'UTF-8') ?>
    <?php if($info['justificacion']): ?>
      <br><strong>Justificación:</strong> <?= htmlspecialchars($info['justificacion'], ENT_QUOTES,'UTF-8') ?>
    <?php endif; ?>
  </div>

  <h3 style="margin-top:15px;">Detalle de Productos</h3>
  <table border="1" cellpadding="4" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11pt;">
    <!-- Definimos anchos de columna -->
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
        <th style="padding:6px; border:1px solid #333;">#</th>
        <th style="padding:2px 4px; border:1px solid #333; text-align:left;">Producto</th>
        <th style="padding:2px 4px; border:1px solid #333; text-align:center;">Cant.</th>
        <th style="padding:2px 4px; border:1px solid #333; text-align:right;">Precio</th>
        <th style="padding:2px 4px; border:1px solid #333; text-align:right;">Dscto</th>
        <th style="padding:2px 4px; border:1px solid #333; text-align:right;">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($detalle as $i => $p): ?>
        <tr <?= $i % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
          <td style="padding:2px 4px; border:1px solid #333;"><?= $i + 1 ?></td>
          <td style="padding:2px 4px; border:1px solid #333;"><?= htmlspecialchars($p['producto'], ENT_QUOTES,'UTF-8') ?></td>
          <td style="padding:2px 4px; border:1px solid #333; text-align:center;"><?= $p['cantidad'] ?></td>
          <td style="padding:2px 4px; border:1px solid #333; text-align:right;"><?= number_format($p['precio'], 2) ?></td>
          <td style="padding:2px 4px; border:1px solid #333; text-align:right;"><?= number_format($p['descuento'], 2) ?></td>
          <td style="padding:2px 4px; border:1px solid #333; text-align:right;"><?= number_format($p['total_producto'], 2) ?></td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($detalle)): ?>
        <tr>
          <td colspan="6" style="text-align:center; padding:6px; border:1px solid #333;">
            No hay productos
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</page>
