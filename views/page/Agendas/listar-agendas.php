<?php
// views/page/agendas/listar-agendas.php

const NAMEVIEW = "Recordatorios";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main">
<div class="card mb-3 border">
    <div class="card-header">
        <h4>Registrar Nuevo Recordatorio</h4>
    </div>
    <div class="card-body">
      <div class="row g-3 align-items-end">
      <div class="col-md-4">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control input" id="cliente" placeholder="Cliente">
              <input type="hidden" id="hiddenIdCliente" name="idcliente">
              <label for="cliente">Cliente</label>
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#ModalCliente">…</button>
            </div>
          </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="date" id="new-fecha" class="form-control input"/>
                <label for="new-fecha" class="form-label">Próxima Visita</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" id="new-comentario" class="form-control input" placeholder="Escribe un comentario…"/>
                <label for="new-comentario" class="form-label">Comentario</label>
            </div>
        </div>
      </div>
    </div>
    <div class="card-footer text-end">
    <button id="btn-new-recordatorio"  class="btn btn-sm btn-success">Registrar</button>
    </div>
  </div>

    <div class="card border">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-9 mb-2 mb-md-0">
                    <!-- Botones de filtro: Semana, Mes -->
                     <div class="d-flex align-items-center">
                         <div class="btn-group me-2" role="group">
                             <button type="button" data-modo="semana" class="btn btn-primary">Semana</button>
                             <button type="button" data-modo="mes" class="btn btn-primary">Mes</button>
                             <button type="button" class="btn btn-outline-danger">
                                 <i class="fa-solid fa-file-pdf"></i>
                                </button>
                            </div>
                        
                    
                        <select id="estadoSelect" class="form-select ms-2 input w-auto" style="color:black; background-color:white;">
                            <option value="A">Activos</option>
                            <option value="P">Pendientes</option>
                            <option value="R">Reprogramados</option>
                            <option value="C">Cancelados</option>
                            <option value="H">Hechos</option>
                        </select>
                    
                </div>
                </div>
                
                <div class="col-md-3 text-md-end">
                    <!-- Fecha -->
                    <div class="input-group">
                        <input type="date" id="Fecha" class="form-control input" />
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="miTabla" class="table table-striped display">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Próxima Visita</th>
                            <th>Comentario</th>
                            <th>Estado</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="ModalCliente" tabindex="-1" aria-labelledby="ModalClienteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header">
        <h2 class="modal-title" id="ModalClienteLabel">Seleccionar Cliente</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <!-- Forzamos tipo “persona” -->
        <input type="hidden" id="tipoBusquedaCliente" value="persona">

        <!-- Método de búsqueda -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <select id="selectMetodoCliente" class="form-select" style="background-color: white;color:black;">
                <option value="dni">DNI</option>
                <option value="nombre">Nombre</option>
              </select>
              <label for="selectMetodoCliente">Método de búsqueda</label>
            </div>
          </div>
        </div>

        <!-- Valor buscado -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <input type="text" class="form-control input" id="vbuscadoCliente" style="background-color: white;" placeholder="Valor buscado" autofocus>
              <label for="vbuscadoCliente">Valor buscado</label>
            </div>
          </div>
        </div>
        <!-- Resultados -->
        <p class="mt-3"><strong>Resultado:</strong></p>
        <div class="table-responsive">
          <table
            id="tabla-resultado-cliente"
            class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Confirmar</th>
              </tr>
            </thead>
            <tbody>
              <!-- Se llena dinámicamente -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>

<!-- Modal Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Tel. Principal:</strong> <span id="det-tel1"></span></p>
                <p><strong>Tel. Alternativo:</strong> <span id="det-tel2"></span></p>
                <p><strong>Correo:</strong> <span id="det-email"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reprogramar -->
<div class="modal fade" id="modalReprog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reprogramar Recordatorio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="date" id="reprog-fecha" class="form-control mb-3" style="color:black; background-color:white;" />
                <button id="btnReprogSave" class="btn btn-primary">Guardar fecha</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Estado -->
<div class="modal fade" id="modalEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="estado-nuevo" class="form-select mb-3" style="color:black; background-color:white;"></select>
                <button id="btnEstadoSave" class="btn btn-primary">Guardar estado</button>
            </div>
        </div>
    </div>
</div>

<?php require_once "../../partials/_footer.php"; ?>


<script>
document.addEventListener('DOMContentLoaded', () => {
  const API      = '<?= SERVERURL ?>app/controllers/Agenda.controller.php';
  const fechaIn  = document.getElementById('new-fecha');
  const comentIn = document.getElementById('new-comentario');
  const btnNew   = document.getElementById('btn-new-recordatorio');
  const hiddenCliente = document.getElementById('hiddenIdCliente');

  // calcular mañana y fijar min/value
  const hoy    = new Date();
  const manana = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate() + 1);
  const pad    = n => String(n).padStart(2,'0');
  const strM   = `${manana.getFullYear()}-${pad(manana.getMonth()+1)}-${pad(manana.getDate())}`;
  fechaIn.min   = strM;
  fechaIn.value = strM;

  btnNew.addEventListener('click', async () => {
    const f = fechaIn.value;
    const c = comentIn.value.trim();
    const idc = hiddenCliente.value;

    if (!idc) {
      return alert('Selecciona primero un cliente.');
    }
    if (!f || !c) {
      return alert('Completa fecha y comentario.');
    }

    // confirmación
    if (!confirm('¿Seguro que quieres registrar este recordatorio?')) {
      return;
    }

    const res = await fetch(API, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        action: 'register',
        idpropietario: idc,
        fchproxvisita: f,
        comentario: c
      })
    });
    const js = await res.json();
    if (js.status === 'success') {
      comentIn.value = '';
      showToast('Orden registrada exitosamente', 'SUCCESS', 1500);
        // Después de 1 segundo (una vez visible el toast) redirige:
        setTimeout(() => {
            cargar();
        }, 1000);
       // recarga tu tabla
    } else {
      alert('No se pudo registrar: ' + js.message);
    }
  });

  // ... aquí sigue el resto de tu JS (filtros, cargar(), etc.) ...
});
</script>


<script>
    // Manejo de modals: detalle, reprogramar y cambiar estado con restricciones

    document.addEventListener('DOMContentLoaded', () => {
        const API = '<?= SERVERURL ?>app/controllers/agenda.controller.php';
        const tablaBody = document.querySelector('#miTabla tbody');
        const btnSemana = document.querySelector('button[data-modo="semana"]');
        const btnMes = document.querySelector('button[data-modo="mes"]');
        const filtros = [btnSemana, btnMes];
        const selectEstado = document.getElementById('estadoSelect');
        const fechaInput = document.getElementById('Fecha');
        const selectNuevo = document.getElementById('estado-nuevo');
        const inputReprog = document.getElementById('reprog-fecha');

        let selectedId;
        let selectedRec;
        let currentModo = 'dia';
        let currentEstado = selectEstado.value;

        const fmtDate = iso => {
            if (!iso) return '';
            const d = new Date(iso);
            const pad = v => String(v).padStart(2, '0');
            return `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()}`;
        };

        // Definir botones según estado
        const estadoBtn = (est, id) => {
            const map = {
                'P': 'btn-warning',
                'R': 'btn-info',
                'C': 'btn-danger',
                'H': 'btn-success'
            };
            const iconos = {
                'P': '<i class="fa-solid fa-clock"></i>',
                'H': '<i class="fa-solid fa-check"></i>',
                'R': '<i class="fa-solid fa-calendar-day"></i>',
                'C': '<i class="fa-solid fa-xmark"></i>'
            };
            const titulos = {
                'P': 'Pendiente',
                'H': 'Realizado',
                'R': 'Reprogramado',
                'C': 'Cancelado'
            };
            const clase = map[est] || 'btn-secondary';
            // Deshabilitar cambio si cancelado o hecho
            const disabled = (est === 'C' || est === 'H') ? 'disabled' : `data-id=\"${id}\" data-action=\"estado\"`;
            return `<button class="btn btn-sm ${clase}" title="${titulos[est]}" ${disabled}>${iconos[est]}</button>`;
        };

        // Pintar tabla aplicando restricciones
        const pintar = data => {
            window._agendaData = data;
            tablaBody.innerHTML = '';
            data.forEach((r, i) => {
                const btnDetail = `<button class="btn btn-sm btn-secondary" data-id="${r.idagenda}" data-action="detail"><i class="fa fa-phone"></i></button>`;
                const btnReprog = (r.estado === 'C' || r.estado === 'H') ? '' : `<button class="btn btn-sm btn-primary" data-id="${r.idagenda}" data-action="reprog"><i class="fa fa-calendar"></i></button>`;
                const btnEstado = estadoBtn(r.estado, r.idagenda);
                tablaBody.insertAdjacentHTML('beforeend', `
        <tr>
          <td>${i+1}</td>
          <td>${r.nomcliente}</td>
          <td>${fmtDate(r.fchproxvisita)}</td>
          <td>${r.comentario}</td>
          <td>${btnEstado}</td>
          <td>${btnDetail} ${btnReprog}</td>
        </tr>
      `);
            });
        };

        // Carga datos según filtros
        const cargar = async () => {
            const url = `${API}?task=listByPeriod&modo=${currentModo}&fecha=${fechaInput.value}&estado=${currentEstado}`;
            const res = await fetch(url);
            const json = await res.json();
            if (json.status === 'success') pintar(json.data);
        };

        // Eventos de filtros
        filtros.forEach(b => b.addEventListener('click', () => {
            currentModo = b.dataset.modo;
            filtros.forEach(x => x.classList.toggle('active', x === b));
            cargar();
        }));
        fechaInput.addEventListener('change', () => {
            currentModo = 'dia';
            filtros.forEach(x => x.classList.remove('active'));
            cargar();
        });
        selectEstado.addEventListener('change', () => {
            currentEstado = selectEstado.value;
            cargar();
        });

        // Delegación de acciones
        tablaBody.addEventListener('click', ev => {
            const btn = ev.target.closest('button[data-action]');
            if (!btn) return;
            const action = btn.dataset.action;
            selectedId = btn.dataset.id;
            selectedRec = window._agendaData.find(r => r.idagenda == selectedId);

            switch (action) {
                case 'detail':
                    document.getElementById('det-tel1').textContent = selectedRec.telprincipal;
                    document.getElementById('det-tel2').textContent = selectedRec.telalternativo;
                    document.getElementById('det-email').textContent = selectedRec.correo;
                    new bootstrap.Modal(document.getElementById('modalDetalle')).show();
                    break;
                case 'reprog':
                    // 1) calculamos “mañana”
                    const hoy = new Date();
                    const manana = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate() + 1);
                    const yyyy = manana.getFullYear();
                    const mm = String(manana.getMonth() + 1).padStart(2, '0');
                    const dd = String(manana.getDate()).padStart(2, '0');
                    const strManana = `${yyyy}-${mm}-${dd}`;

                    // 2) fijamos el mínimo y el valor por defecto
                    inputReprog.min = strManana;
                    inputReprog.value = strManana;

                    // 3) abrimos el modal
                    new bootstrap.Modal(document.getElementById('modalReprog')).show();
                    break;

                case 'estado':
                    // Llenar select de nuevos estados según estado actual
                    selectNuevo.innerHTML = '';
                    let opciones = [];
                    if (selectedRec.estado === 'P') opciones = [
                        ['C', 'Cancelado'],
                        ['H', 'Hecho']
                    ];
                    else if (selectedRec.estado === 'R') opciones = [
                        ['C', 'Cancelado'],
                        ['H', 'Hecho']
                    ];
                    opciones.forEach(opt => {
                        const o = document.createElement('option');
                        o.value = opt[0];
                        o.text = opt[1];
                        selectNuevo.appendChild(o);
                    });
                    new bootstrap.Modal(document.getElementById('modalEstado')).show();
                    break;
            }
        });

        // Guardar reprogramación
        document.getElementById('btnReprogSave').addEventListener('click', async () => {
            const nueva = document.getElementById('reprog-fecha').value;
            await fetch(API, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'reprogramar',
                    idagenda: selectedId,
                    nueva_fecha: nueva
                })
            });
            bootstrap.Modal.getInstance(document.getElementById('modalReprog')).hide();
            cargar();
        });

        // Guardar cambio de estado
        document.getElementById('btnEstadoSave').addEventListener('click', async () => {
            const nuevo = selectNuevo.value;
            await fetch(API, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'updateEstado',
                    idagenda: selectedId,
                    estado: nuevo
                })
            });
            bootstrap.Modal.getInstance(document.getElementById('modalEstado')).hide();
            cargar();
        });

        // Inicialización
        fechaInput.value = new Date().toISOString().split('T')[0];
        cargar();
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const API_CLIENTE = '<?= SERVERURL ?>app/controllers/propietario.controller.php';
  
  // Elementos del modal
  const selectMetodo    = document.getElementById('selectMetodoCliente');
  const vbuscado        = document.getElementById('vbuscadoCliente');
  const tablaCuerpo     = document.querySelector('#tabla-resultado-cliente tbody');
  const hiddenIdCliente = document.getElementById('hiddenIdCliente');
  const inputCliente    = document.getElementById('cliente');
  const modalClienteEl  = document.getElementById('ModalCliente');
  
  let clienteTimer = null;

  // Función que consulta al backend y dibuja resultados
  function buscarCliente() {
    const tipo   = document.getElementById('tipoBusquedaCliente').value; // “persona”
    const metodo = selectMetodo.value;
    const valor  = vbuscado.value.trim();
    if (!valor) {
      tablaCuerpo.innerHTML = '';
      return;
    }
    fetch(`${API_CLIENTE}?task=buscarPropietario&tipo=${tipo}&metodo=${metodo}&valor=${encodeURIComponent(valor)}`)
      .then(res => res.json())
      .then(data => {
        tablaCuerpo.innerHTML = data.map((item,i) => `
          <tr>
            <td>${i+1}</td>
            <td>${item.nombre}</td>
            <td>${item.documento}</td>
            <td>
              <button class="btn btn-sm btn-success" data-id="${item.idcliente}">
                <i class="fa-solid fa-circle-check"></i>
              </button>
            </td>
          </tr>
        `).join('');
      })
      .catch(console.error);
  }

  // Al tipear o cambiar método, disparamos búsqueda con debounce
  vbuscado.addEventListener('input', () => {
    clearTimeout(clienteTimer);
    clienteTimer = setTimeout(buscarCliente, 300);
  });
  selectMetodo.addEventListener('change', () => {
    clearTimeout(clienteTimer);
    clienteTimer = setTimeout(buscarCliente, 300);
  });

  // Cuando se haga clic en “Confirmar” dentro de la tabla de resultados
  tablaCuerpo.addEventListener('click', e => {
    const btn = e.target.closest('button[data-id]');
    if (!btn) return;
    const id     = btn.dataset.id;
    const nombre = btn.closest('tr').cells[1].textContent;

    // Seteamos el campo oculto y el input visible
    hiddenIdCliente.value = id;
    inputCliente.value    = nombre;

    // Cerramos el modal tras un pequeño delay
    setTimeout(() => {
      bootstrap.Modal.getOrCreateInstance(modalClienteEl).hide();
    }, 100);
  });

  // Opcional: poner foco en el input al mostrarse el modal
  modalClienteEl.addEventListener('shown.bs.modal', () => {
    vbuscado.focus();
  });
});
</script>
