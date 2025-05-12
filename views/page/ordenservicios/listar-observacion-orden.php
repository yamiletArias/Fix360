<?php
const NAMEVIEW = "Observaciones de la Orden de Registro";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
require_once "../../../app/models/Observacion.php";
$obsModel  = new Observacion();
$idorden   = intval($_GET['idorden'] ?? 0);
$observes  = $obsModel->getObservacionByOrden($idorden);
?>
<style>
  img {
    width: 1500px;
    height: 150px;
  }

  .form-check-input {
    transform: scale(1.5);
  }

</style>

<div class="container-main">
  <!-- FORMULARIO DE REGISTRO -->
  <form
    id="formObservacion"
    method="POST"
    action="<?= SERVERURL ?>app/controllers/observacion.controller.php?task=add"
    enctype="multipart/form-data">
    <input type="hidden" name="idorden" value="<?= $idorden ?>">
    <div class="card">
      <div class="card-header">
        <h3><strong>Registrar Observación</strong></h3>
      </div>
      <div class="card-body">
        <div class="row">
          <!-- Select componente -->
          <div class="col-md-4">
            <div class="form-floating">
              <select
                class="form-select"
                name="idcomponente"
                id="componente"
                style="color: black;"
                required>
                <option value="">Seleccione un componente</option>
              </select>
              <label for="componente">Componente:</label>
            </div>
          </div>
          <!-- Input foto -->
          <div class="col-md-4">
            <div class="form-floating">
              <input
                type="file"
                class="btn btn-outline-dark border input-img"
                name="foto"
                id="foto"
                accept="image/png, image/jpeg"
                >
            </div>
          </div>
          <!-- Checkbox estado -->
          <div class="col-md-4 d-flex align-items-center">
            <div class="form-check form-switch">
              <input
                class="form-check-input"
                type="checkbox"
                name="estado"
                id="estado"
                checked>
              <label class="form-check-label ms-2" for="estado">
                <strong>Estado</strong>
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer text-end mb-4">
        <button type="submit" class="btn btn-success btn-sm">Registrar</button>
      </div>
    </div>
  </form>

  <!-- MOSTRAR OBSERVACIÓN GENERAL -->
  <div class="mb-4 d-flex align-items-start gap-2">
    <div class="flex-grow-1">
      <label for="obs-general" class="form-label"><strong>Observación de la orden:</strong></label>
      <textarea
        id="obs-general"
        class="form-control input"
        rows="2"
        disabled><?= htmlspecialchars($observes[0]['observacion_orden'] ?? '') ?></textarea>
    </div>
  </div>

  <!-- CONTENEDOR DE TARJETAS INDIVIDUALES -->
  <div id="obs-container" class="d-flex flex-wrap gap-4"></div>

  <div>
    <a href="listar-ordenes.php" class="btn btn-secondary">Volver</a>
  </div>
</div>
</div>

<!-- Modal de edición -->
<div class="modal fade" id="editObservacionModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="formEditObservacion" class="modal-content" enctype="multipart/form-data">
      <input type="hidden" name="oldFoto" id="edit-oldFoto">
      <div class="modal-header">
        <h5 class="modal-title">Editar Observación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="idobservacion" id="edit-idobservacion">
        <div class="mb-3">
          <label for="edit-componente" class="form-label">Componente</label>
          <select class="form-select input" name="idcomponente" id="edit-componente" style="color:black;background-color:white;" required>
            <!-- se llenará dinámicamente -->
          </select>
        </div>
        <div class="mb-3">
          <label for="edit-foto" class="form-label">Foto (opcional)</label>
          <input type="file" class="form-control" name="foto" id="edit-foto" style="color:black;background-color:white;"  accept="image/jpeg,image/png">
        </div>
        <div class="form-check form-switch">
          <label class="form-check-label" for="edit-estado"><strong>Estado:</strong></label>
          <input class="form-check-input" type="checkbox" id="edit-estado" name="estado">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>


<?php require_once "../../partials/_footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // REFERENCIAS GLOBALES
  const selectComp   = document.getElementById('componente');
  const cont         = document.getElementById('obs-container');
  const modalSelect  = document.getElementById('edit-componente');
  const formAdd      = document.getElementById('formObservacion');
  const formEdit     = document.getElementById('formEditObservacion');
  const editModalEl  = document.getElementById('editObservacionModal');
  const bsEditModal  = new bootstrap.Modal(editModalEl);
  const idorden      = new URLSearchParams(location.search).get('idorden');

  // 1) Carga de componentes en ambos selects
  fetch('http://localhost/fix360/app/controllers/componente.controller.php?task=getAll')
    .then(r => r.ok ? r.json() : Promise.reject(r.status))
    .then(lista => {
      [selectComp, modalSelect].forEach(sel => {
        sel.innerHTML = '<option value="">Seleccione un componente</option>';
        lista.forEach(c => {
          const opt = document.createElement('option');
          opt.value   = c.idcomponente;
          opt.text    = c.componente;
          sel.appendChild(opt);
        });
      });
    })
    .catch(err => {
      console.error('Error cargando componentes:', err);
      selectComp.innerHTML = modalSelect.innerHTML = '<option value="">Error</option>';
    });

  // 2) Cargar observaciones existentes
  if (!idorden) {
    cont.innerHTML = '<p class="text-danger">No se indicó ningún ID de orden.</p>';
  } else {
    fetch(`http://localhost/fix360/app/controllers/observacion.controller.php?task=getObservacionByOrden&idorden=${idorden}`)
      .then(r => r.ok ? r.json() : Promise.reject(r.status))
      .then(data => {
        cont.innerHTML = data.length === 0
          ? `<div class="alert alert-warning">No hay observaciones por mostrar</div>`
          : data.map(o => `
          
            <div class="card" data-idobservacion="${o.idobservacion}" data-foto="${o.foto}" style="width:18rem; margin:10px;">
              <div class="card-header"><strong>${o.componente}</strong></div>
              <img src="<?= SERVERURL ?>${o.foto}" class="card-img-top" style="height:150px; object-fit:cover;">
              <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <label class="me-2 mb-0"><strong>Estado</strong></label>
                  <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" ${o.estado ? 'checked' : ''} disabled>
                  </div>
                </div>
                <div>
                  <button class="btn btn-warning btn-sm me-1 btn-edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button class="btn btn-danger btn-sm btn-delete">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          `).join('');

          console.log('Primer card:', cont.querySelector('.card').outerHTML);
console.log('Dataset:', cont.querySelector('.card').dataset);
      })
      .catch(err => {
        console.error(err);
        cont.innerHTML = `<div class="alert alert-warning" role="alert">
  No hay observaciones por cargar
</div>`;
      });
  }

  // 3) Registro de nueva observación
  formAdd.addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    try {
      const res  = await fetch('http://localhost/fix360/app/controllers/observacion.controller.php?task=add', { method: 'POST', body: fd });
      const json = await res.json();
      if (!res.ok || json.error) throw new Error(json.error || res.status);
      Swal.fire('Registrado', json.success, 'success').then(() => window.location.reload());
    } catch (err) {
      Swal.fire('Error al registrar', err.message, 'error');
    }
  });

  // 4) Delegación: BORRAR con confirmación
  cont.addEventListener('click', e => {
    const btn = e.target.closest('.btn-delete');
    if (!btn) return;
    const idobs = btn.closest('.card').dataset.idobservacion;
    Swal.fire({
      position: 'center',
      title: '¿Eliminar observación?',
      text: 'Esta acción no se puede deshacer',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, borrar',
      cancelButtonText: 'Cancelar'
    }).then(({ isConfirmed }) => {
      if (!isConfirmed) return;
      fetch(`http://localhost/fix360/app/controllers/observacion.controller.php?task=deleteObservacion&idobservacion=${idobs}`)
        .then(r => r.ok ? r.json() : Promise.reject(r.status))
        .then(json => {
          if (json.success) {
            btn.closest('.card').remove();
            Swal.fire('Borrado', json.success, 'success');
          } else {
            Swal.fire('Error', json.error || 'No se pudo borrar', 'error');
          }
        })
        .catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
    });
  });

  // 5) Delegación: EDITAR → abre modal y precarga datos
  cont.addEventListener('click', e => {
  const btn = e.target.closest('.btn-edit');
  if (!btn) return;
  const card    = btn.closest('.card');
  const idobs   = card.dataset.idobservacion;
  const comp    = card.querySelector('.card-header strong').textContent;
  const estado  = card.querySelector('.form-check-input').checked;
  const fotoVieja = card.dataset.foto;

  document.getElementById('edit-idobservacion').value = idobs;
  document.getElementById('edit-oldFoto').value      = fotoVieja;
  Array.from(modalSelect.options).find(o => o.text === comp).selected = true;
  document.getElementById('edit-estado').checked     = estado;
  document.getElementById('edit-foto').value         = '';
  document.getElementById('edit-oldFoto').value = fotoVieja;
  bsEditModal.show();
});


  // 6) Submit edición con confirmación
  formEdit.addEventListener('submit', e => {
    e.preventDefault();
    Swal.fire({
      position: 'center',
      title: '¿Confirmar cambios?',
      text: '¿Estás seguro de que quieres guardar los cambios?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, guardar',
      cancelButtonText: 'Cancelar'
    }).then(async ({ isConfirmed }) => {
      if (!isConfirmed) return;
      const fd = new FormData(formEdit);
      fd.append('task', 'updateObservacion');
      try {
        const res = await fetch(
  'http://localhost/fix360/app/controllers/observacion.controller.php?task=updateObservacion',
  {
    method: 'POST',
    body: fd
  }
);

        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.error || res.status);
        Swal.fire('Actualizado', json.success, 'success').then(() => window.location.reload());
      } catch (err) {
        Swal.fire('Error', err.message, 'error');
      }
    });
  });

});
</script>
