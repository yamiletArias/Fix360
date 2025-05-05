<?php

const NAMEVIEW = "Ordenes de Servicio";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main">
  <div class="card border">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col-md-3 mb-2 mb-md-0">
          <!-- Botones de filtro: Día, Semana, Mes -->
          <div class="btn-group" role="group" aria-label="Basic example">
            <button type="button" data-modo="semana" class="btn btn-primary text-white">Semana</button>
            <button type="button" data-modo="mes" class="btn btn-primary text-white">Mes</button>
            <button type="button" class="btn btn-danger text-white">
              <i class="fa-solid fa-file-pdf"></i>
            </button>
          </div>
        </div>
        <div class="col-md-6"></div>
        <div class="col-md-3 text-md-end">
          <!-- Input para la fecha y botón -->
          <div class="input-group">
            <input type="date" class="form-control input" aria-label="Fecha" aria-describedby="button-addon2" id="Fecha">
            <a href="registrar-ordenes2.php" class="btn btn-success text-center" type="button" id="button-addon2">Registrar</a>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="table-container">
        <table id="miTabla" class="table table-striped display">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th>Propietario</th>
              <th>Cliente</th>
              <th>Fch. Ingreso</th>
              <th>Fch. Salida</th>
              <th>Placa</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>

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

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const fechaInput = document.getElementById('Fecha');
    const tablaBody = document.querySelector('#miTabla tbody');
    const btnSemana = document.querySelector('button[data-modo="semana"]');
    const btnMes = document.querySelector('button[data-modo="mes"]');
    const filtros = [btnSemana, btnMes];
    const API = 'http://localhost/fix360/app/controllers/ordenservicio.controller.php';

    // utilitario para formatear
    const fmt = iso => {
      if (!iso) return '';
      const d = new Date(iso);
      const pad = v => String(v).padStart(2, '0');
      return `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()} ` +
        `${pad(d.getHours())}:${pad(d.getMinutes())}`;
    };

    // pinta el resultado
    const pintar = data => {
      tablaBody.innerHTML = '';
      data.forEach((o, i) => {
        tablaBody.insertAdjacentHTML('beforeend', `
        <tr>
          <td class="text-center">${i+1}</td>
          <td>${o.propietario||''}</td>
          <td>${o.cliente    ||''}</td>
          <td>${fmt(o.fechaingreso)}</td>
          <td>${ o.fechasalida ? fmt(o.fechasalida) : '<span class="text-muted">Servicios en desarrollo</span>'}</td>
          <td>${o.placa ||''}</td>
          <td>
            <button class="btn btn-sm btn-danger"       title="Eliminar orden"  data-id="${o.idorden}" data-action="eliminar"><i class="fa-solid fa-trash"></i></button>
            <button class="btn btn-sm btn-info"         title="Ver detalle de orden"  data-id="${o.idorden}" data-action="detalle"><i class="fa-solid fa-clipboard-list"></i></button>
            <button class="btn btn-sm btn-primary"      title="Observaciones de la orden" data-id="${o.idorden}" data-action="ver"><i class="fa-solid fa-eye"></i></button>
            <button class="btn btn-sm btn-outline-dark" title="Asignar fecha de salida"  data-id="${o.idorden}" data-action="salida"><i class="fa-solid fa-calendar-days"></i></button>
          </td>
        </tr>`);
      });
    };

    // Llama al endpoint y pinta
    const cargar = async (modo, fecha) => {
      console.log(`> cargando modo=${modo} fecha=${fecha}`);
      try {
        const res = await fetch(`${API}?modo=${modo}&fecha=${fecha}`);
        console.log('HTTP status:', res.status);
        const json = await res.json();
        console.log('JSON recibido:', json);
        if (json.status === 'success') pintar(json.data);
        else console.error('Error listando:', json.message);
      } catch (e) {
        console.error('Fetch error:', e);
      }
    };

    // marca botones
    const marcaActivo = btn => {
      filtros.forEach(b => b.classList.toggle('active', b === btn));
    };

    // event-delegation para acciones de fila
    tablaBody.addEventListener('click', async ev => {
      const btn = ev.target.closest('button[data-action]');
      if (!btn) return;
      const id = btn.dataset.id;
      if (btn.dataset.action === 'eliminar' && await ask('¿Confirma eliminar orden?', 'Orden')) {
        // ...
        showToast('Orden eliminada', 'SUCCESS');
        cargar(currentModo, fechaInput.value);
      }
      // … dentro de tu tablaBody.addEventListener('click', async ev => { … })
if (btn.dataset.action === 'salida' && await ask('¿Confirma fecha de salida?', 'Orden')) {
  try {
    // Llamada al controlador para setFechaSalida
    const res = await fetch(API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'setSalida',
        idorden: id
      })
    });
    const json = await res.json();

    if (json.status === 'success' && json.updated > 0) {
      showToast('Fecha de salida registrada', 'SUCCESS');
    } else {
      showToast('No se pudo registrar la salida: ' + (json.message || 'error desconocido'), 'ERROR');
    }
  } catch (e) {
    console.error('Error al asignar fecha de salida:', e);
    showToast('Error de red al asignar salida', 'ERROR');
  }
  // Refrescar la tabla
  cargar(currentModo, fechaInput.value);
}

      // etc.
    });

    // inicializo en día
    const hoy = new Date().toISOString().split('T')[0];
    fechaInput.value = hoy;
    let currentModo = 'dia';
    cargar(currentModo, hoy);

    // clicks en Semana/Mes
    filtros.forEach(btn => {
      btn.addEventListener('click', () => {
        currentModo = btn.dataset.modo;
        marcaActivo(btn);
        cargar(currentModo, fechaInput.value);
      });
    });

    // cambio de fecha → Día
    fechaInput.addEventListener('change', () => {
      currentModo = 'dia';
      marcaActivo(null);
      cargar(currentModo, fechaInput.value);
    });
  });
</script>

</body>

</html>