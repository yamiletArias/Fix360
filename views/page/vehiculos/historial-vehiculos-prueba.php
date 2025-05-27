<?php

const NAMEVIEW = "Historial del Vehiculo";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

$id = intval($_GET['id'] ?? 0);

?>
<style>
  .card .card-body {
     padding: 10px; 
}

.tab-content{
padding-bottom: 10px;

}
</style>

<div class="container-main">
  <a class="btn btn-sm btn-secondary mb-3" href="listar-vehiculos.php">volver</a>

  <div class="row">
    <!-- Propietario Actual -->
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
          <h5 class="mb-0"><i class="fa-solid fa-user"></i> Datos del Propietario Actual</h5>
        </div>
        <div class="card-body" >
          <p><strong>Nombre:</strong> <span id="prop-nombre">Cargando...</span></p>
          <p><strong>Documento:</strong> <span id="prop-doc">Cargando...</span></p>
          <p><strong>Tel. Principal:</strong> <span id="prop-tel1">Cargando...</span></p>
          <p><strong>Tel. Alternativo:</strong> <span id="prop-tel2">Cargando...</span></p>
          <p><strong>Email:</strong> <span id="prop-email">Cargando...</span></p>
        </div>
      </div>
    </div>
    <!-- Vehículo -->
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="fa-solid fa-car"></i> Datos del Vehículo</h5>
        </div>
        <div class="card-body">
          <p><strong>Modelo:</strong> <span id="vh-modelo">Cargando...</span></p>
          <p><strong>Año:</strong> <span id="vh-anio">Cargando...</span></p>
          <p><strong>Placa:</strong> <span id="vh-placa">Cargando...</span></p>
          <p><strong>Color:</strong> <span id="vh-color">Cargando...</span></p>
          <p><strong>Combustible:</strong> <span id="vh-combustible">Cargando...</span></p>
          <p><strong>Últ. Modificación:</strong> <span id="vh-modificado">Cargando...</span></p>
          <p><strong>VIN:</strong> <span id="vh-vin">Cargando...</span></p>
          <p><strong>N° Chasis:</strong> <span id="vh-chasis">Cargando...</span></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Switch entre Órdenes y Ventas -->

  <ul class="nav nav-tabs mb-3" id="histTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="tab-ordenes" data-bs-toggle="tab" data-bs-target="#content-ordenes" type="button" role="tab">Órdenes de Servicio
        <i class="fa-solid fa-car-tunnel menu-icon"></i>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tab-ventas" data-bs-toggle="tab" data-bs-target="#content-ventas" type="button" role="tab">Ventas
        <i class="fa-solid fa-tags menu-icon"></i>
      </button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="content-ordenes" role="tabpanel">
      <div class="card mb-4">
        <div class="card-body">
          <table id="tablaOrdenes" class="table table-striped table-hover display w-100">
            <thead>
              <tr>
                <th>#</th><th>Ingreso</th><th>Salida</th><th>Kms</th><th>Grua</th><th>Estado</th><th>Téc.</th><th>Total Mano</th><th>Total Rep.</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="content-ventas" role="tabpanel">
      <div class="card mb-4">
        <div class="card-body">
          <table id="tablaVentas" class="table table-striped table-hover display w-100">
            <thead>
              <tr>
                <th>#</th><th>Fecha</th><th>Tipo</th><th>Comprobante</th><th>Moneda</th><th>Kms</th><th>Vendedor</th><th>Total Neto</th><th>Items</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const vehiculoId = <?= $id ?>;
    const clean = v => (v == null || v === '' ? '—' : v);

    // Cargar datos generales + propietario
    fetch(`<?= SERVERURL ?>app/controllers/Vehiculo.controller.php?task=getHistorial&idvehiculo=${vehiculoId}`)
      .then(res => res.json())
      .then(data => {
        const g = data.general;
        document.getElementById("vh-modelo").textContent      = clean(g.modelo);
        document.getElementById("vh-anio").textContent        = clean(g.anio);
        document.getElementById("vh-placa").textContent       = clean(g.placa);
        document.getElementById("vh-color").textContent       = clean(g.color);
        document.getElementById("vh-combustible").textContent = clean(g.tcombustible);
        document.getElementById("vh-modificado").textContent  = clean(g.modificado);
        document.getElementById("vh-vin").textContent         = clean(g.vin);
        document.getElementById("vh-chasis").textContent      = clean(g.numchasis);
        const p = data.propietario;
        document.getElementById("prop-nombre").textContent    = clean(p.propietario);
        document.getElementById("prop-doc").textContent       = clean(p.documento_propietario);
        document.getElementById("prop-tel1").textContent      = clean(p.telprincipal);
        document.getElementById("prop-tel2").textContent      = clean(p.telalternativo);
        document.getElementById("prop-email").textContent     = clean(p.correo);
      })
      .catch(err => console.error("Error cargando historial:", err));

    // Inicializar DataTables sin buscador
    $("#tablaOrdenes").DataTable({
      ajax: { url: `<?= SERVERURL ?>app/controllers/OrdenServicio.controller.php?task=listByVehiculo&id=${vehiculoId}`, dataSrc: "" },
      searching: false,
      columns: [
        { data: null, render: (d,t,r,m)=> m.row+1 },
        { data: "fechaingreso", render: d=> clean(d) },
        { data: "fechasalida", render: d=> clean(d) },
        { data: "kilometraje" },
        { data: "ingresogrua", render: d=> d ? "Sí":"No" },
        { data: "estado" },
        { data: "tecnico" },
        { data: "total_mano_obra" },
        { data: "total_repuestos" }
      ],
      language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json" }
    });

    $("#tablaVentas").DataTable({
      ajax: { url: `<?= SERVERURL ?>app/controllers/Venta.controller.php?task=listByVehiculo&id=${vehiculoId}`, dataSrc: "" },
      searching: false,
      columns: [
        { data: null, render: (d,t,r,m)=> m.row+1 },
        { data: "fechahora", render: d=> clean(d) },
        { data: "tipocom" },
        { data: "comprobante" },
        { data: "moneda" },
        { data: "kilometraje", render: d=> clean(d) },
        { data: "vendedor" },
        { data: "total_neto" },
        { data: "items_vendidos" }
      ],
      language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json" }
    });
  });
</script>
