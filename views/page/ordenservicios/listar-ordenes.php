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
            <button type="button" data-modo="dia" class="btn btn-primary text-white">Día</button>
            <button type="button" data-modo="semana" class="btn btn-primary text-white">Semana</button>
            <button type="button" data-modo="mes" class="btn btn-primary text-white">Mes</button>
            <button id="btnVerEliminados"
              type="button"
              class="btn btn-secondary text-white"
              title="Ver eliminados"
              data-estado="A">
              <i class="fa-solid fa-eye-slash"></i>
            </button>

            <!-- Exportar PDF -->
            <button type="button"
              class="btn btn-danger text-white"
              title="Exportar PDF">
              <i class="fa-solid fa-file-pdf"></i>
            </button>
          </div>
        </div>
        <div class="col-md-6"></div>
        <div class="col-md-3 text-md-end">
          <!-- Input para la fecha y botón -->
          <div class="input-group">
            <input type="date" class="form-control input" aria-label="Fecha" aria-describedby="button-addon2" id="Fecha">
            <!--a href="registrar-ordenes2.php" class="btn btn-success text-center" type="button" id="button-addon2">Registrar</!--a-->
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

<div class="modal fade" id="modalJustificacion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formJustificacion" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Justificación de Eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="jIdOrden">
        <div class="mb-3">
          <label for="jTexto" class="form-label">Justificación:</label>
          <textarea id="jTexto" class="form-control" rows="3" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalDetalleOrden" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Detalle de la Orden</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Cabecera -->
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" id="dCliente" class="form-control input" disabled>
              <label for="dCliente">Cliente</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" id="dPropietario" class="form-control input" disabled>
              <label for="dPropietario">Propietario</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" id="dVehiculo" class="form-control input" disabled>
              <label for="dVehiculo">Vehículo</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" id="dKilometraje" class="form-control input" disabled>
              <label for="dKilometraje">Kilometraje</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" id="dIngreso" class="form-control input" disabled>
              <label for="dIngreso">Fecha Ingreso</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" id="dSalida" class="form-control input" disabled>
              <label for="dSalida">Fecha Salida</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="dGrua" style="margin-left:10px; transform: scale(1.4);" disabled>
              <label class="input form-check-label" for="dGrua" style="padding-left:30px ;color:black;opacity:1;font-size:16px;">Ingreso por grúa</label>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label">Observaciones</label>
            <textarea id="dObservaciones" class="form-control input" rows="2" disabled></textarea>
          </div>
        </div>
        <!-- Detalle de servicios -->
        <div class="table-responsive mb-3">
          <table class="table table-bordered" id="tablaDetalle">
            <thead>
              <tr>
                <th>#</th>
                <th>Servicio</th>
                <th>Mecánico</th>
                <th>Precio</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <!-- Total -->
        <div class="text-end">
          <strong>Total: </strong><span id="dTotal">0.00</span>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
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
    const btnsModo = document.querySelectorAll('button[data-modo]');
    const btnVerElim = document.getElementById('btnVerEliminados');
    const API = 'http://localhost/fix360/app/controllers/ordenservicio.controller.php';

    // --- Modal Justificación ---
    const modalJust = new bootstrap.Modal(document.getElementById('modalJustificacion'));
    const formJust = document.getElementById('formJustificacion');
    const jIdOrden = document.getElementById('jIdOrden');
    const jTexto = document.getElementById('jTexto');

    // --- Modal Detalle de Orden ---
    const modalDet = new bootstrap.Modal(document.getElementById('modalDetalleOrden'));
    const dCliente = document.getElementById('dCliente');
    const dProp = document.getElementById('dPropietario');
    const dVeh = document.getElementById('dVehiculo');
    const dKilo = document.getElementById('dKilometraje');
    const dIngreso = document.getElementById('dIngreso');
    const dSalida = document.getElementById('dSalida');
    const dGrua = document.getElementById('dGrua');
    const dObs = document.getElementById('dObservaciones');
    const tablaDet = document.querySelector('#tablaDetalle tbody');
    const dTotal = document.getElementById('dTotal');

    let currentModo = 'dia';
    let currentEstado = 'A'; // 'A' activas, 'D' eliminadas

    // --- Helpers ---
    const pad = v => String(v).padStart(2, '0');
    const fmt = iso => {
      if (!iso) return '';
      const d = new Date(iso);
      return `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()} ` +
        `${pad(d.getHours())}:${pad(d.getMinutes())}`;
    };
    const isToday = iso => {
      if (!iso) return false;
      const d = new Date(iso),
        t = new Date();
      return d.getDate() === t.getDate() &&
        d.getMonth() === t.getMonth() &&
        d.getFullYear() === t.getFullYear();
    };

    // --- Pinta la tabla principal ---
    const pintar = data => {
      if ($.fn.DataTable.isDataTable('#miTabla')) {
        $('#miTabla').DataTable().destroy();
      }
      tablaBody.innerHTML = '';
      data.forEach((o, i) => {
        const btnDetalle = `<button class="btn btn-sm btn-info" title="Detalle" data-id="${o.idorden}" data-action="detalle">
                             <i class="fa-solid fa-clipboard-list"></i>
                           </button>`;
        const btnVer = `<a class="btn btn-sm btn-primary" href="listar-observacion-orden.php?idorden=${o.idorden}">
                             <i class="fa-solid fa-eye"></i>
                           </a>`;
        let btnEliminar = '',
          btnSalida = '';
        if (!o.fechasalida) {
          btnSalida = `<button class="btn btn-sm btn-outline-dark" data-id="${o.idorden}" data-action="salida">
                       <i class="fa-solid fa-calendar-days"></i>
                     </button>`;
          if (isToday(o.fechaingreso)) {
            btnEliminar = `<button class="btn btn-sm btn-danger" data-id="${o.idorden}" data-action="eliminar">
                           <i class="fa-solid fa-trash"></i>
                         </button>`;
          }
        }
        tablaBody.insertAdjacentHTML('beforeend', `
        <tr>
          <td class="text-center">${i+1}</td>
          <td>${o.propietario||''}</td>
          <td>${o.cliente||''}</td>
          <td>${fmt(o.fechaingreso)}</td>
          <td>${o.fechasalida? fmt(o.fechasalida) : '<span class="text-muted">En progreso</span>'}</td>
          <td>${o.placa||''}</td>
          <td>
            <div class="d-flex gap-1 justify-content-center">
              ${btnEliminar}${btnDetalle}${btnVer}${btnSalida}
            </div>
          </td>
        </tr>
      `);
      });
      $('#miTabla').DataTable({
        paging: true,
        searching: true,
        info: true,
        columnDefs: [{
          orderable: false,
          targets: -1
        }],
        language: {
          lengthMenu: "Mostrar _MENU_ por página",
          zeroRecords: "No hay resultados",
          info: "Mostrando página _PAGE_ de _PAGES_",
          search: "Buscar:",
          emptyTable: "No hay datos"
        }
      });
    };

    // --- Cambia icono y estado del toggle ---
    const actualizarToggleEstado = () => {
      if (currentEstado === 'A') {
        btnVerElim.classList.replace('btn-warning', 'btn-secondary');
        btnVerElim.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
        btnVerElim.title = 'Ver eliminados';
      } else {
        btnVerElim.classList.replace('btn-secondary', 'btn-warning');
        btnVerElim.innerHTML = '<i class="fa-solid fa-eye"></i>';
        btnVerElim.title = 'Ver activos';
      }
    };

    // --- Trae datos y pinta ---
    const cargar = async (modo, fecha) => {
      try {
        const res = await fetch(`${API}?modo=${modo}&fecha=${fecha}&estado=${currentEstado}`);
        const json = await res.json();
        if (json.status === 'success') pintar(json.data);
        else console.error(json.message);
      } catch (e) {
        console.error('Fetch error:', e);
      }
    };

    // --- Delegación de clicks en la tabla ---
    tablaBody.addEventListener('click', async ev => {
      const btn = ev.target.closest('button[data-action]');
      if (!btn) return;
      const id = btn.dataset.id;

      // Eliminar
      if (btn.dataset.action === 'eliminar') {
        jIdOrden.value = id;
        jTexto.value = '';
        modalJust.show();
        return;
      }

      // Registrar salida
      if (btn.dataset.action === 'salida') {
        if (!confirm('¿Registrar fecha de salida?')) return;
        try {
          const resp = await fetch(API, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              action: 'setSalida',
              idorden: id
            })
          });
          const js = await resp.json();
          if (js.status === 'success') showToast('Salida registrada', 'SUCCESS');
          else showToast('Error al registrar', 'ERROR');
        } catch {
          showToast('Error de red', 'ERROR');
        }
        cargar(currentModo, fechaInput.value);
        return;
      }

      // Detalle
      if (btn.dataset.action === 'detalle') {
        try {
          const resp = await fetch(`${API}?action=getDetalle&idorden=${id}`);
          const js = await resp.json();
          const {
            cabecera,
            detalle,
            total
          } = js.data;

          // Cabecera
          dCliente.value = cabecera.cliente || '';
          dProp.value = cabecera.propietario || '';
          dVeh.value = cabecera.vehiculo || '';
          dKilo.value = cabecera.kilometraje || '';
          dIngreso.value = cabecera.fecha_ingreso || '';
          dSalida.value = cabecera.fecha_salida || '';
          dGrua.checked = !!cabecera.ingresogrua;
          dObs.value = cabecera.observaciones || '';

          // Detalle de servicios
          tablaDet.innerHTML = '';
          detalle.forEach((r, i) => {
            tablaDet.insertAdjacentHTML('beforeend', `
            <tr>
              <td>${i+1}</td>
              <td>${r.servicio}</td>
              <td>${r.mecanico}</td>
              <td class="text-end">${parseFloat(r.precio).toFixed(2)}</td>
            </tr>
          `);
          });

          // Total
          dTotal.textContent = parseFloat(total).toFixed(2);

          modalDet.show();
        } catch (e) {
          console.error('Error detalle:', e);
        }
      }
    });

    // --- Envío del formulario de justificación ---
    formJust.addEventListener('submit', async ev => {
      ev.preventDefault();
      const idord = parseInt(jIdOrden.value, 10);
      const just = jTexto.value.trim();
      if (!just) return;
      try {
        const resp = await fetch(API, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            action: 'eliminar',
            idorden: idord,
            justificacion: just
          })
        });
        const js = await resp.json();
        if (js.status === 'success') {
          showToast('Orden eliminada', 'SUCCESS');
          cargar(currentModo, fechaInput.value);
        } else {
          showToast('Error: ' + js.message, 'ERROR');
        }
      } catch {
        showToast('Error de red', 'ERROR');
      } finally {
        modalJust.hide();
      }
    });

    // --- Listeners de filtros y toggle ---
    btnVerElim.addEventListener('click', () => {
      currentEstado = currentEstado === 'A' ? 'D' : 'A';
      actualizarToggleEstado();
      cargar(currentModo, fechaInput.value);
    });
    btnsModo.forEach(b => b.addEventListener('click', () => {
      currentModo = b.dataset.modo;
      btnsModo.forEach(x => x.classList.toggle('active', x === b));
      cargar(currentModo, fechaInput.value);
    }));
    fechaInput.addEventListener('change', () => {
      currentModo = 'dia';
      btnsModo.forEach(x => x.classList.remove('active'));
      cargar(currentModo, fechaInput.value);
    });

    // --- Inicialización ---
    fechaInput.value = new Date().toISOString().slice(0, 10);
    actualizarToggleEstado();
    cargar(currentModo, fechaInput.value);
  });
</script>



</body>

</html>