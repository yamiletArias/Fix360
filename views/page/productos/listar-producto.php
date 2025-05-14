<?php

const NAMEVIEW = "Productos";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div class="header-group">
    <div class="text-end">
      <button type="button" onclick="window.location.href='./registrar-productos.php'" class="btn btn-success text-end">
        Registrar
      </button>
    </div>
  </div>
  <div class="table-container" id="tablaProductosContainer">
    <table id="tablaProductos" class="table table-striped display">
      <thead>
        <tr>
          <th>#</th>
          <th>Marca</th>
          <th>Subcategoria</th>
          <th>Descripcion</th>
          <th>Precio</th>
          <th>Presentacion</th>
          <th>Cantidad</th>
          <th>Und. Medida</th>
          <th>Imagen</th>
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

<?php

require_once "../../partials/_footer.php";

?>

<script>
  // 1) Url base (PHP -> JS)
  const SERVERURL = "<?= SERVERURL ?>";

  // 2) Opciones de Lightbox (opcional)
  lightbox.option({
    'resizeDuration': 200,
    'wrapAround': true
  });

  // 3) Inicialización de DataTable
  $(document).ready(function() {
    cargarTablaProductos();
  });
</script>


<script>
  function cargarTablaProductos() { // Inicio de cargarTablaProductos()
    if ($.fn.DataTable.isDataTable("#tablaProductos")) {
      $("#tablaProductos").DataTable().destroy();
    } // Cierra if

    $("#tablaProductos").DataTable({ // Inicio de configuración DataTable para productos
      ajax: {
        url: "http://localhost/fix360/app/controllers/producto.controller.php?task=getAll", // URL que retorna JSON con los productos
        dataSrc: ""
      }, // Cierra ajax
      columns: [{ // Columna 1: Número de fila
          data: null,
          render: (data, type, row, meta) => meta.row + 1
        }, // Cierra columna 1
        { // Columna 2: Marca
          data: "marca",
          defaultContent: "Sin marca"
        }, // Cierra columna 2
        { // Columna 3: Subcategoria
          data: "subcategoria",
          defaultContent: "Sin subcategoría"
        }, // Cierra columna 3
        { // Columna 4: Descripción
          data: "descripcion",
          defaultContent: "Sin descripción"
        }, // Cierra columna 4
        { // Columna 5: Precio
          data: "precio",
          defaultContent: "0.00"
        }, // Cierra columna 5
        { // Columna 6: Presentación
          data: "presentacion",
          defaultContent: "Sin presentación"
        }, // Cierra columna 6
        // Cierra columna 7
        { // Columna 8: Cantidad
          data: "cantidad",
          defaultContent: "0"
        },
        { // Columna 7: Unidad de Medida (medida)
          data: "medida",
          defaultContent: "Sin medida"
        }, // Cierra columna 8
        { // Columna 9: Imagen
          data: "img",
          render: function(data, type, row) {
            if (data && data.trim() !== "") {
              const imgUrl = data.startsWith('http') ?
                data :
                `${SERVERURL.replace(/\/$/, '')}/${data.replace(/^\/+/, '')}`;

              console.log('Lightbox imgUrl:', imgUrl); // <— comprueba que apunte correctamente

              return `
      <a href="${imgUrl}"
         data-lightbox="productos"
         data-title="${row.descripcion}">
        <img src="${imgUrl}"
             alt="${row.descripcion}"
             style="width:50px; border-radius:0%;" />
      </a>
    `;
            }
            return "Sin imagen";
          }

        },
        // Cierra columna 9
        { // Columna 10: Opciones (botones para editar y eliminar)
          data: null,
          render: function(data, type, row) { // Inicio de render de opciones
            return `
              <a href="editar-productos.php?id=${row.idproducto}"   class="btn btn-sm btn-warning" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
              <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${row.idproducto})" title="Eliminar">
                <i class="fa-solid fa-trash"></i>
              </button>
            `;
          } // Cierra render de opciones
        } // Cierra columna 10
      ], // Cierra columns
      language: { // Inicio de configuración de idioma
        "lengthMenu": "Mostrar _MENU_ registros por página",
        "zeroRecords": "No se encontraron resultados",
        "info": "Mostrando página _PAGE_ de _PAGES_",
        "infoEmpty": "No hay registros disponibles",
        "infoFiltered": "(filtrado de _MAX_ registros totales)",
        "search": "Buscar:",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "emptyTable": "No hay datos disponibles en la tabla"
      },
      drawCallback: function(settings) {
        // Esto vuelve a enlazar Lightbox con los <a data-lightbox> recién creados
        if (window.lightbox && typeof lightbox.init === 'function') {
          lightbox.init();
        }
      } // Cierra language
    }); // Cierra DataTable inicialización
  } // Cierra cargarTablaProductos()


  // Ejemplo de función para eliminar producto (debes implementar la lógica en el controlador)
  function eliminarProducto(idproducto) { // Inicio de eliminarProducto()
    if (confirm("¿Estás seguro de eliminar el producto?")) {
      // Lógica de eliminación vía fetch o redirección según tu implementación
      console.log("Eliminar producto con ID:", idproducto);
      // Aquí podrías hacer una solicitud AJAX al controlador para eliminar el producto
    }
  } // Cierra eliminarProducto()
</script>


</body>

</html>