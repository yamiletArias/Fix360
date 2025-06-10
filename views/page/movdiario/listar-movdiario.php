<?php

const NAMEVIEW = "Movimientos del Dia";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
require_once "../../../app/models/OrdenServicio.php";
require_once "../../../app/models/Servicio.php";

setlocale(LC_TIME, 'spanish');

// Instanciar modelos
$ordenModel    = new OrdenServicio();
$servicioModel = new Servicio();

// Obtener totales de órdenes
try {
  $ordenesActivas = $ordenModel->getTotalOrdenesActivas();
  $ordenesHoy     = $ordenModel->getTotalOrdenesHoy();
} catch (Exception $e) {
  $ordenesActivas = 0;
  $ordenesHoy     = 0;
}

// Obtener servicios mensuales
try {
  $serviciosData = $servicioModel->getServiciosMensuales();
} catch (Exception $e) {
  $serviciosData = [];
}

// Filtrar mes actual
$mesActual = date('Y-m');
$labels    = [];
$data      = [];
$totalMes  = 0;
foreach ($serviciosData as $item) {
  if ($item['mes'] === $mesActual) {
    $labels[]   = $item['servicio'];
    $data[]     = (int)$item['veces_realizado'];
    $totalMes  += (int)$item['veces_realizado'];
  }
}

?>
<div class="container-main">

  <h2><?= "{$saludo}, " . htmlspecialchars($usuario['nombreCompleto']); ?></h2>
  <br>

  <div class="col-md-12 grid-margin border">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <div class="d-sm-flex align-items-baseline report-summary-header">
              <h5 class="font-weight-semibold">Resumen Diario:</h5>
            </div>
          </div>
        </div>
        <div class="row report-inner-cards-wrapper">
          <div class="col-md-6 col-xl report-inner-card">
            <div class="inner-card-text">
              <span class="report-title">Órdenes Activas</span>
              <h4><?= $ordenesActivas ?></h4>
            </div>
            <div class="inner-card-icon bg-success">
              <i class="fa-solid fa-car-rear"></i>
            </div>
          </div>
          <div class="col-md-6 col-xl report-inner-card">
            <div class="inner-card-text">
              <span class="report-title">Órdenes de Hoy</span>
              <h4><?= $ordenesHoy ?></h4>
            </div>
            <div class="inner-card-icon bg-info">
              <i class="fa-solid fa-calendar-day"></i>
            </div>
          </div>
          <div class="col-md-6 col-xl report-inner-card">
            <div class="inner-card-text">
              <span class="report-title">Servicios realizados este mes</span>
              <h4><?= $totalMes ?></h4>
            </div>
            <div class="inner-card-icon bg-warning">
              <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <h3>Servicios realizados en <?= ucfirst(strftime('%B %Y')) ?>:</h3>
  <div class="mb-4">
    <canvas id="myChart"></canvas>
  </div>

  <!-- ← NUEVO: tarjeta con tabla de resumen -->
  <div class="card mb-4 border">
    <div class="card-header">
      <strong>Resumen de Servicios</strong>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="tablaResumenServicios" class="table table-bordered">
          <thead>
            <tr>
              <th><strong>Servicio</strong></th>
              <th><strong>Veces realizado</strong></th>
            </tr>
          </thead>
          <tbody>
            <!-- Se rellenará desde JS -->
          </tbody>
          <tfoot>
            <tr>
              <th>Total</th>
              <th id="totalServicios"><?= $totalMes ?></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
  <!-- FIN NUEVO -->

</div>
</div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const canvas = document.getElementById('myChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    // datos PHP a JS
    const labels = <?= json_encode($labels) ?>;
    const datos  = <?= json_encode($data) ?>;

    // 1) Pintar gráfico
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Veces realizado',
          data: datos,
          backgroundColor: colorPalette,
          borderColor:     colorPalette,
          borderWidth: 1
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } }
      }
    });

    // 2) Rellenar tabla de resumen
    const tbody = document.querySelector('#tablaResumenServicios tbody');
    tbody.innerHTML = ''; // vaciar por si acaso

    labels.forEach((servicio, idx) => {
      const tr = document.createElement('tr');

      const tdServ = document.createElement('td');
      tdServ.textContent = servicio;
      tr.appendChild(tdServ);

      const tdVeces = document.createElement('td');
      tdVeces.textContent = datos[idx];
      tr.appendChild(tdVeces);

      tbody.appendChild(tr);
    });

    // (El total ya lo pusimos en PHP)
  });
</script>
</body>
</html>
