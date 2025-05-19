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

    <!-- Aquí obligamos el estilo en línea para la tabla -->
    <table border="1" cellpadding="6" cellspacing="0" width="100%" >
      <thead>
        <tr>
          <th style="padding:6px; border:1px solid #333;">#</th>
          <th style="padding:6px; border:1px solid #333;">Fecha</th>
          <th style="padding:6px; border:1px solid #333;">Flujo</th>
          <th style="padding:6px; border:1px solid #333;">Tipo</th>
          <th style="padding:6px; border:1px solid #333;">Cantidad</th>
          <th style="padding:6px; border:1px solid #333;">Saldo</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($datos as $i => $fila): ?>
          <tr <?= $i % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
            <td style="padding:6px; border:1px solid #333;"><?= $i + 1 ?></td>
            <td style="padding:6px; border:1px solid #333;"><?= date('d/m/Y', strtotime($fila['fecha'])) ?></td>
            <td style="padding:6px; border:1px solid #333;"><?= htmlspecialchars($fila['flujo'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="padding:6px; border:1px solid #333;"><?= htmlspecialchars($fila['tipo_movimiento'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="padding:6px; border:1px solid #333; text-align:right;"><?= htmlspecialchars($fila['cantidad'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="padding:6px; border:1px solid #333; text-align:right;"><?= htmlspecialchars($fila['saldo_restante'], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($datos)): ?>
          <tr>
            <td colspan="6" style="text-align:center; padding:6px; border:1px solid #333;">
              No hay movimientos en este periodo
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</page>
