<?php
const NAMEVIEW = "Graficos de Contactabilidad";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main">

  <!-- 1) Controles para seleccionar Período y Fecha Desde/Hasta -->
  <div class="row mb-4">
    <div class="form-floating col-md-3">
      <select id="selectPeriodo" class="form-control input" style="color:black;">
        <option value="ANUAL">Anual</option>
        <option value="MENSUAL">Mensual</option>
        <option value="SEMANAL">Semanal</option>
      </select>
      <label for="selectPeriodo" style="margin-left: 5px;"><strong>Periodo:</strong></label>
    </div>
    <div class="form-floating col-md-3">
      <input type="date" id="inputDesde" class="form-control input  " />
      <label for="inputDesde" style="margin-left: 5px;"><strong>Desde:</strong></label>
    </div>
    <div class="form-floating col-md-3">
      <input type="date" id="inputHasta" class="form-control input" />
      <label for="inputHasta" style="margin-left: 5px;"><strong>Hasta:</strong></label>
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button id="btnActualizar" class="btn btn-primary w-100">
        Mostrar
      </button>
    </div>
  </div>

  <!-- 2) Gráfico de barras: Contactabilidad agrupada -->
  <div class="mb-4">
    <canvas id="myChart" style="margin-bottom: 50px;"></canvas>
  </div>

  <!-- 3) Card con tabla de resumen -->
  <div class="card">
    <div class="card-header">
      <strong>Resumen de datos</strong>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="tablaResumen" class="table table-bordered">
          <thead>
            <!-- Se genera dinámicamente desde JS -->
          </thead>
          <tbody>
            <!-- Se genera dinámicamente desde JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
</div>

<?php
require_once "../../partials/_footer.php";
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const selectPeriodo = document.getElementById('selectPeriodo');
  const inputDesde = document.getElementById('inputDesde');
  const inputHasta = document.getElementById('inputHasta');
  const btnActualizar = document.getElementById('btnActualizar');
  const ctxContactabilidad = document.getElementById('myChart');
  const tablaResumen = document.getElementById('tablaResumen');


  let chartContactabilidad = new Chart(ctxContactabilidad, {
    type: 'bar',
    data: {
      labels: [],
      datasets: []
    },
    options: {
      responsive: true,
      scales: {
        x: {
          stacked: false,
          title: {
            display: true,
            text: 'Período'
          }
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Total de Clientes'
          }
        }
      },
      plugins: {
        title: {
          display: true,
          text: 'Distribución de Clientes por Contactabilidad'
        }
      }
    }
  });

  function nombreMes(esLabel) {
    const [anno, mes] = esLabel.split("-");
    const mesesArr = [
      'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    return mesesArr[parseInt(mes, 10) - 1] + ' ' + anno;
  }

  async function actualizarGraficos() {
    const periodo = selectPeriodo.value;
    const desde = inputDesde.value;
    const hasta = inputHasta.value;

    if (!desde || !hasta) {
      alert('Por favor, seleccione las fechas "Desde" y "Hasta".');
      return;
    }
    if (desde > hasta) {
      alert('"Desde" no puede ser mayor que "Hasta".');
      return;
    }

    const formData = new URLSearchParams();
    formData.append('operation', 'getGraficoContactabilidad');
    formData.append('periodo', periodo);
    formData.append('fecha_desde', desde);
    formData.append('fecha_hasta', hasta);

    try {
      const response = await fetch('<?= SERVERURL ?>app/controllers/Contactabilidad.Controller.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData.toString()
      });
      const datos = await response.json();

      if (datos.error) {
        console.error(datos.error);
        alert('Error del servidor: ' + datos.error);
        return;
      }

      const periodosSet = new Set();
      const canalesSet = new Set();
      datos.forEach(row => {
        periodosSet.add(row.periodo_label);
        canalesSet.add(row.contactabilidad);
      });
      const periodos = Array.from(periodosSet).sort();
      const canales = Array.from(canalesSet).sort();

      const conteosPorCanal = {};
      canales.forEach(canal => {
        conteosPorCanal[canal] = new Array(periodos.length).fill(0);
      });
      datos.forEach(row => {
        const idxPeriodo = periodos.indexOf(row.periodo_label);
        const canal = row.contactabilidad;
        const total = parseInt(row.total_clientes, 10) || 0;
        conteosPorCanal[canal][idxPeriodo] = total;
      });

      chartContactabilidad.data.labels = periodos;
      chartContactabilidad.data.datasets = canales.map((canal, i) => {
        return {
          label: canal,
          data: conteosPorCanal[canal],
          backgroundColor: [
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)'
          ][i % 6],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
          ][i % 6],
          borderWidth: 1
        };
      });
      chartContactabilidad.update();

      tablaResumen.querySelector('thead').innerHTML = '';
      tablaResumen.querySelector('tbody').innerHTML = '';

      // a) Crear encabezado: “Período” + cada canal + “Total”
      const theadTr = document.createElement('tr');
      const thPeriodo = document.createElement('th');
      thPeriodo.textContent = 'Período';
      theadTr.appendChild(thPeriodo);

      canales.forEach(canal => {
        const th = document.createElement('th');
        th.textContent = canal;
        theadTr.appendChild(th);
      });

      const thTotal = document.createElement('th');
      thTotal.textContent = 'Total';
      theadTr.appendChild(thTotal);

      tablaResumen.querySelector('thead').appendChild(theadTr);

      periodos.forEach((periodoLabel, idx) => {
        const tr = document.createElement('tr');

        const tdPer = document.createElement('td');
        if (/^\d{4}-\d{2}$/.test(periodoLabel)) {
          tdPer.textContent = nombreMes(periodoLabel);
        } else {
          tdPer.textContent = periodoLabel;
        }
        tr.appendChild(tdPer);

        let sumaEnEstaFila = 0;
        canales.forEach(canal => {
          const valor = conteosPorCanal[canal][idx];
          const td = document.createElement('td');
          td.textContent = valor;
          tr.appendChild(td);
          sumaEnEstaFila += valor;
        });

        const tdTotal = document.createElement('td');
        tdTotal.textContent = sumaEnEstaFila;
        tdTotal.style.fontWeight = 'bold';
        tr.appendChild(tdTotal);

        tablaResumen.querySelector('tbody').appendChild(tr);
      });

    } catch (err) {
      console.error('Error en fetch o parseo de JSON:', err);
      alert('Ocurrió un error al obtener los datos para el gráfico.');
    }
  }

  window.addEventListener('DOMContentLoaded', () => {
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, '0');
    const dd = String(hoy.getDate()).padStart(2, '0');

    inputDesde.value = `${yyyy}-${mm}-01`;
    inputHasta.value = `${yyyy}-${mm}-${dd}`;

    actualizarGraficos();
  });

  btnActualizar.addEventListener('click', () => {
    actualizarGraficos();
  });
</script>