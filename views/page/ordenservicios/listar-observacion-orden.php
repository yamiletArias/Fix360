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
  img { width: 1500px; height: 150px; }
  .form-check-input { transform: scale(1.5); }
</style>

<div class="container-main">
  <!-- FORMULARIO DE REGISTRO -->
  <form
    id="formObservacion"
    method="POST"
    action="<?= SERVERURL ?>app/controllers/observacion.controller.php?task=add"
    enctype="multipart/form-data"
  >
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
                required
              >
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
                required
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
                checked
              >
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
        class="form-control"
        rows="2"
        disabled
      ><?= htmlspecialchars($observes[0]['observacion_orden'] ?? '') ?></textarea>
    </div>
  </div>

  <!-- CONTENEDOR DE TARJETAS INDIVIDUALES -->
  <div id="obs-container" class="d-flex flex-wrap gap-4"></div>

  <div>
    <a href="listar-ordenes.php" class="btn btn-secondary">Volver</a>
  </div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const selectComp = document.getElementById('componente');
    const cont = document.getElementById('obs-container');
    const idorden = new URLSearchParams(location.search).get('idorden');

    // 1) Llenar select de componentes
    fetch('http://localhost/fix360/app/controllers/componente.controller.php?task=getAll')
      .then(r => r.ok ? r.json() : Promise.reject(`HTTP ${r.status}`))
      .then(lista => {
        selectComp.innerHTML = '<option value="">Seleccione un componente</option>';
        lista.forEach(c => {
          const opt = document.createElement('option');
          opt.value = c.idcomponente;
          opt.textContent = c.componente;
          selectComp.appendChild(opt);
        });
      })
      .catch(err => {
        console.error('Error cargando componentes:', err);
        selectComp.innerHTML = '<option value="">Error cargando componentes</option>';
      });

    // 2) Cargar observaciones existentes
    if (!idorden) {
      cont.innerHTML = '<p class="text-danger">No se indicó ningún ID de orden.</p>';
      return;
    }
    fetch(`http://localhost/fix360/app/controllers/observacion.controller.php?task=getObservacionByOrden&idorden=${idorden}`)
      .then(r => r.ok ? r.json() : Promise.reject(`HTTP ${r.status}`))
      .then(data => {
        cont.innerHTML = data.length === 0
          ? `<div class="alert alert-warning">No hay observaciones por mostrar</div>`
          : data.map(o => `
            <div class="card" style="width:18rem; margin:10px">
              <div class="card-header"><strong>${o.componente}</strong></div>
              <img src="<?= SERVERURL ?>${o.foto}" class="card-img-top" alt="Foto no proporcionada">
              <div class="card-footer p-2">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" ${o.estado ? 'checked' : ''} disabled>
                    <label class="form-check-label ms-2"><strong>Estado</strong></label>
                  </div>
                  <div>
                    <button class="btn btn-warning btn-sm me-1"
                      onclick="location.href='editar-observacion-ordenes.php?idobs=${o.idobservacion}&idorden=${idorden}'">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm"
                      onclick="if(confirm('¿Borrar esta observación?')) location.href='eliminar-observacion.php?idobs=${o.idobservacion}&idorden=${idorden}'">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          `).join('');
      })
      .catch(err => {
        console.error('Error al cargar observaciones:', err);
        cont.innerHTML = '<p class="text-danger">Error al cargar las observaciones.</p>';
      });

    // 3) Envío del formulario con FormData
    document.getElementById('formObservacion').addEventListener('submit', async e => {
      e.preventDefault();
      const fd = new FormData(e.target);
      try {
        const res = await fetch('http://localhost/fix360/app/controllers/observacion.controller.php?task=add', {
          method: 'POST',
          body: fd
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.error || `HTTP ${res.status}`);
        alert(json.success);
        window.location.reload();
      } catch (err) {
        console.error(err);
        alert('Error al registrar: ' + err.message);
      }
    });
  });
</script>
