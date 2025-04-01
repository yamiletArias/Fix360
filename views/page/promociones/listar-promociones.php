<?php

const NAMEVIEW = "Promociones";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div class="header-group">
    <div>
      <button
        type="button"
        onclick="window.location.href='./registrar-promociones.php'"
        class="btn btn-success">
        Registrar
      </button>
    </div>
  </div>
  <div class="container-main">
    <div style="display: flex; gap: 20px; flex-wrap: wrap">
      <div class="card" style="width: 18rem">
        <h5 class="card-title text-center"><strong> Aceite 4x3 </strong></h5>
        <img
          src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTT_Jp6rhIdZP4F67YwRSmSxWQ_zRmwtjX_4Q&s"
          class="card-img-top"
          alt="..." />
        <div class="card-body">
          <div class="form-check form-switch">
            <label style="display: block"><strong> Desde: </strong><span>10/03/2025</span></label>
            <label style="display: block"><strong> Hasta: </strong><span>16/03/2025</span></label>
            <label style="display: block"><strong> Cantidad: </strong><span>12</span></label>
            <label style="display: block"><strong> Precio: </strong> <span> 90.32</span></label>
            <label style="display: block"><strong> Descripcion: </strong>
              <span>
                Sustancia grasa, líquida a temperatura ordinaria, de
                mayor o menor viscosidad, no miscible con agua y de
                menor densidad</span></label>
          </div>
          <h4 style="margin-top: 30px">Opciones:</h4>
          <button
            title="Editar"
            onclick="window.location.href='editar-promociones.html'"
            class="btn btn-warning btn-sm">
            <i class="fa-solid fa-pen-to-square"></i>
          </button>
          <button
            title="Eliminar"
            class="btn btn-danger btn-sm"
            id="btnEliminar"
            data-id="data-123">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>

      <div class="card" style="width: 18rem">
        <h5 class="card-title text-center"><strong>Filtros 3x2</strong></h5>
        <img
          src="https://asteclub.mx/wp-content/uploads/2024/04/composicion-diferentes-accesorios-coche-min.jpg"
          class="card-img-top"
          alt="..." />
        <div class="card-body">
          <div class="form-check form-switch">
            <label style="display: block"><strong> Desde: </strong> <span>12/03/2025</span></label>
            <label style="display: block"><strong> Hasta: </strong> <span>15/03/2025</span></label>
            <label style="display: block"><strong> Cantidad: </strong> <span>20</span></label>
            <label style="display: block"><strong> Precio: </strong> <span>45.50</span></label>
            <label style="display: block"><strong> Descripcion: </strong>
              <span>Filtros de aceite y aire para diferentes modelos de
                vehículos.</span></label>
          </div>
          <h4 style="margin-top: 30px">Opciones:</h4>
          <button
            title="Editar"
            onclick="window.location.href='editar-promociones.html'"
            class="btn btn-warning btn-sm">
            <i class="fa-solid fa-pen-to-square"></i>
          </button>
          <button
            title="Eliminar"
            class="btn btn-danger btn-sm"
            id="btnEliminar"
            data-id="data-123">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>
      <div class="card" style="width: 18rem">
        <h5 class="card-title text-center"><strong>Bujías 4x3</strong></h5>
        <img
          src="https://mail.nitro.pe/images/2015/junio/bujias_correctas_para_los_autos.jpg"
          class="card-img-top"
          alt="..." />
        <div class="card-body">
          <div class="form-check form-switch">
            <label style="display: block"><strong> Desde: </strong> <span>15/03/2025</span></label>
            <label style="display: block"><strong> Hasta: </strong> <span>22/03/2025</span></label>
            <label style="display: block"><strong> Cantidad: </strong> <span>30</span></label>
            <label style="display: block"><strong> Precio: </strong> <span>85.50</span></label>
            <label style="display: block"><strong> Descripcion: </strong>
              <span>Bujías de iridio de larga duración. Mejora el
                rendimiento del motor y reduce el consumo de
                combustible.</span></label>
          </div>
          <h4 style="margin-top: 30px">Opciones:</h4>
          <button
            title="Editar"
            onclick="window.location.href='editar-promociones.html'"
            class="btn btn-warning btn-sm">
            <i class="fa-solid fa-pen-to-square"></i>
          </button>
          <button
            title="Eliminar"
            class="btn btn-danger btn-sm"
            id="btnEliminar"
            data-id="data-123">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>
      <div class="card" style="width: 18rem">
        <h5 class="card-title text-center"><strong>Liquido de frenos</strong></h5>
        <img
          src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ1-8s_WRZu1ZeiTzQGtuPyqmvdxMPbm6lKcg&s"
          class="card-img-top"
          alt="..." />
        <div class="card-body">
          <div class="form-check form-switch">
            <label style="display: block"><strong> Desde: </strong> <span>15/03/2025</span></label>
            <label style="display: block"><strong> Hasta: </strong> <span>25/03/2025</span></label>
            <label style="display: block"><strong> Cantidad: </strong><span>20</span></label>
            <label style="display: block"><strong> Precio: </strong> <span>32.99</span></label>
            <label style="display: block"><strong> Descripcion: </strong>
              <span>Líquido de frenos DOT 4 de alta calidad para sistemas
                hidráulicos de frenos y embragues.</span></label>
          </div>
          <h4 style="margin-top: 30px">Opciones:</h4>
          <button
            title="Editar"
            onclick="window.location.href='editar-promociones.html'"
            class="btn btn-warning btn-sm">
            <i class="fa-solid fa-pen-to-square"></i>
          </button>
          <button
            title="Eliminar"
            class="btn btn-danger btn-sm"
            id="btnEliminar"
            data-id="data-123">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
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
  // Agregar evento de eliminación a todos los botones con la clase "btnEliminar"
  $(document).on("click", "#btnEliminar", async function() {
    const id = $(this).data("id"); // ID del registro a eliminar (opcional)

    if (await ask("¿Estás seguro de eliminar esta promocion?", "Promociones")) {
      showToast("Registro eliminado correctamente", "SUCCESS");
      // Aquí podrías agregar la lógica para eliminar el registro
      console.log(`Eliminando registro con ID: ${id}`);
    } else {
      showToast("Operación cancelada", "WARNING");
    }
  });
</script>
<!-- endinject -->
<!-- Custom js for this page -->
<!-- End custom js for this page -->
</body>

</html>