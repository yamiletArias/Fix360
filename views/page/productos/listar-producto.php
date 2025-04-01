<?php

CONST NAMEVIEW = "Productos";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

      <div class="container-main">
        <div class="header-group">
          <div>
            <button type="button" onclick="window.location.href='./registrar-productos.html'" class="btn btn-success">
              Registrar
            </button>
          </div>
        </div>

        <div class="table-container">
          <table id="miTabla" class="table table-striped display">
            <thead>
              <tr>
                <th>#</th>
                <th>Marca</th>
                <th>Subcategoria</th>
                <th>Descripcion</th>
                <th>Precio</th>
                <th>Presentacion</th>
                <th>Und. Medida</th>
                <th>Cantidad</th>
                <th>Imagen</th>
                <th>Opciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td class="text-left">Shell Helix</td>
                <td class="text-left">Aceite para auto</td>
                <td class="text-left">HX7 10W-40</td>
                <td>50.00</td>
                <td>Galonera</td>
                <td>L</td>
                <td class="text-center">4</td>
                <td>
                  <img src="https://densalubricantes.com/wp-content/uploads/2023/10/ShellHelixHX710w-405L.jpg"
                    width="80" alt="" />
                </td>
                <td>
                  <button class="btn btn-danger btn-sm btnEliminar" data-id="data-123">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                  <button title="Editar" onclick="window.location.href='editar-productos.html'" class="btn btn-warning btn-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td class="text-left">Mahindra</td>
                <td class="text-left">Disco de embriague</td>
                <td class="text-left">Funcar</td>
                <td>520.00</td>
                <td>Unidad</td>
                <td>UND</td>
                <td class="text-center">1</td>
                <td>
                  <img src="https://funcar.pe/cdn/shop/files/DISCODEEMBRAGUE.jpg?v=1696824072" width="100" alt="" />
                </td>
                <td>
                  <button class="btn btn-danger btn-sm btnEliminar" data-id="data-123">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                  <button title="Editar" onclick="window.location.href='editar-productos.html'" class="btn btn-warning btn-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>3</td>
                <td class="text-left">Tucson</td>
                <td class="text-left">Filtro de aceite</td>
                <td class="text-left">Elantra(2016 - 2022)</td>
                <td>18.30</td>
                <td>Unidad</td>
                <td>UND</td>
                <td class="text-center">1</td>
                <td>
                  <img src="https://funcar.pe/cdn/shop/products/2630035505_1_1800x.jpg?v=1678053841" 
                    alt="" />
                </td>
                <td>
                  <button class="btn btn-danger btn-sm btnEliminar" data-id="data-123">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                  <button title="Editar" onclick="window.location.href='editar-productos.html'" class="btn btn-warning btn-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
 
  <?php

  require_once "../../partials/_footer.php";
  
 ?>

  <script>
    // Agregar evento de eliminación a todos los botones con la clase "btnEliminar"
    $(document).on("click", ".btnEliminar", async function () {
      const id = $(this).data("id"); // ID del registro a eliminar (opcional)

      if (await ask("¿Estás seguro de eliminar este registro?", "Producto")) {
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