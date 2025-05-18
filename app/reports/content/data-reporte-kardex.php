<style>
  h1 { font-family: helvetica; font-size: 18pt; color: #333; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #888; padding: 4px; font-size: 10pt; }
  .totales { font-weight: bold; }
  .page-number { text-align: right; font-size: 8pt; }
</style>

<page backtop="25mm" backbottom="15mm" backleft="10mm" backright="10mm">
  <page_header>
    <h1 style="font-size:16pt; margin:0">
      Movimientos del día <?= date('d/m/Y') ?>
    </h1>
    <h2 style="font-size:14pt; margin:0">
      de <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>
    </h2>
    <hr>
  </page_header>

  <page_footer>
    <hr>
    <div style="text-align:right; font-size:8pt">
      Página [[page_cu]]/[[page_nb]]
    </div>
  </page_footer>


  <!-- Aquí tu contenido dinámico -->
  

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Fecha</th>
      <th>Flujo</th>
      <th>Tipo</th>
      <th>Cantidad</th>
      <th>Saldo</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($datos as $i => $fila): ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td><?= date('d/m/Y', strtotime($fila['fecha'])) ?></td>
      <td><?= htmlspecialchars($fila['flujo']) ?></td>
      <td><?= htmlspecialchars($fila['tipo_movimiento']) ?></td>
      <td><?= htmlspecialchars($fila['cantidad']) ?></td>
      <td><?= htmlspecialchars($fila['saldo_restante']) ?></td>
    </tr>
  <?php endforeach; ?>
  <?php if (empty($datos)): ?>
    <tr><td colspan="6" style="text-align:center">No hay movimientos en este periodo</td></tr>
  <?php endif; ?>
  </tbody>
</table>

</page>
