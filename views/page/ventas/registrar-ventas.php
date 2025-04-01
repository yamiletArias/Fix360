<?php
const NAMEVIEW = "Registro de ventas";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SESSION VENTAS</title>

  <style>
    .container-ventas {
      background: transparent;
      padding: 30px;
      border-radius: 8px;
      box-shadow: none;
      width: 1500px;
      /* Aumenta el tamaño */
      min-height: 700px;
      /* Aumenta la altura */
      margin-left: 80px;
      /* Lo mueve más a la derecha */
      margin-top: 50px;
    }

    .form-group {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      margin-top: 25px;
      gap: 15px;
    }

    .form-group label {
      margin-right: 15px;
    }

    /* Mantener el borde visible en todo momento incluso al enfocar el campo */
    input:focus,
    select:focus {
      outline: none;
      /* Elimina el contorno predeterminado del navegador */
      border: 1px solid #ccc;
      /* Mantiene el borde visible */
      box-shadow: none;
      /* Elimina cualquier sombra que aparezca al enfocar */
    }

    input,
    select,
    button {
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input[type="text"],
    select {
      flex: 1;
    }

    input[type="date"] {
      width: 160px;
    }

    .small-input {
      width: 130px;
    }

    .medium-input {
      width: 200px;
    }

    .table-container {
      margin-top: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table,
    th,
    td {
      border: 1px solid #ccc;
      text-align: center;
      padding: 10px;
    }

    .btn-container {
      display: flex;
      justify-content: flex-end;
      margin-top: 40px;
    }

    .btn-finalizar {
      background: green;
      color: white;
      padding: 12px;
      border: none;
      cursor: pointer;
      font-size: 16px;
    }

    .header-group {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .right-group {
      display: flex;
      gap: 10px;
    }

    .form-check {
      display: flex;
      align-items: center;
      gap: 5px;
      /* Reducimos el espacio entre el radio button y el texto */
    }

    .form-check-input {
      margin-right: 2px;
      /* Muy poco margen para que el radio button esté casi pegado a la palabra */
    }
  </style>
</head>

<body>
  <!-- venta.php -->
  <div class="container-ventas">
    <!-- Formulario de venta -->
    <form action="Venta.controller.php" method="POST">
      <div class="header-group">
        <div class="form-group">
          <!-- Radio buttons para seleccionar el tipo de comprobante -->
          <label>
            <input name="tipo" type="radio" id="factura" value="factura">
            Factura
          </label>
          <label>
            <input name="tipo" type="radio" id="boleta" value="boleta" checked>
            Boleta
          </label>
          <!-- <div class="form-check">
                  <input class="form-check-input" name="tipo" type="radio" id="factura" value="factura" />
                  <label class="form-check-label" for="factura">Factura</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" name="tipo" type="radio" id="boleta" value="boleta" checked />
                  <label class="form-check-label" for="boleta">Boleta</label>
                </div> -->

        </div>
        <div class="right-group">
          <input type="text" class="small-input" name="numserie" id="numserie" placeholder="N° serie" required
            disabled />
          <input type="text" name="numcomprobante" id="numcom" class="small-input" placeholder="N° comprobante" required
            disabled />
        </div>
      </div>

      <!-- Cliente, fecha y moneda -->
      <div class="form-group">
        <input type="text" class="medium-input" name="nomcliente" id="nomcliente" placeholder="Buscar Cliente" required
          autocomplete="off" data-idcliente="12345" />
        <ul id="clientesResultado" class="search-results"></ul>

        <input type="date" name="fecha" id="fecha" required />

        <select class="small-input" name="tipomoneda" required>
          <option value="Soles">Soles</option>
          <option value="Dolares">Dólares</option>
        </select>
      </div>

      <!-- Productos -->
      <div class="form-group">
        <input type="text" class="medium-input" name="producto" id="producto" placeholder="Buscar Producto" required />
        <input type="number" class="small-input" name="precio" id="precio" placeholder="PRECIO" required />
        <input type="number" class="small-input" name="cantidad" id="cantidad" placeholder="CANTIDAD" required />
        <input type="number" class="small-input" name="descuento" id="descuento" placeholder="DESCUENTO" />
        <button type="button" class="btn btn-success" id="agregarProducto">
          AGREGAR
        </button>
      </div>

      <!-- Tabla de productos agregados -->
      <div class="table-container">
        <table id="miTabla" class="table table-striped display">
          <thead>
            <tr>
              <th>PRODUCTO</th>
              <th>PRECIO</th>
              <th>CANTIDAD</th>
              <th>DSCT</th>
              <th>Importe</th>
              <th>
                <button class="btn btn-danger btn-sm">
                  <i class="fas fa-times"></i>
                </button>
              </th>
            </tr>
          </thead>
          <tbody>
            <!-- productos agregados dinámicamente -->
          </tbody>
        </table>
      </div>

      <!-- Botón para finalizar la venta -->
      <div class="btn-container">
        <button id="finalizarBtn" type="button" class="btn btn-success">
          FINALIZAR
        </button>
      </div>
    </form>
  </div>
  <!--FIN VENTAS-->
  </div>
  </div>

  <!-- plugins:js -->
  <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="../../assets/js/off-canvas.js"></script>
  <script src="../../assets/js/hoverable-collapse.js"></script>
  <script src="../../assets/js/misc.js"></script>
  <script src="../../assets/js/settings.js"></script>
  <script src="../../assets/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  <!-- End custom js for this page -->

  <!-- jQuery (necesario para DataTables) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const numSerieInput = document.getElementById("numserie");
      const numComInput = document.getElementById("numcom");
      const tipoInputs = document.querySelectorAll('input[name="tipo"]');

      // Función para generar un número de serie con formato
      function generateNumber(type) {
        const randomNumber = Math.floor(Math.random() * 100); // Genera un número aleatorio entre 0 y 99
        return `${type}${String(randomNumber).padStart(3, "0")}`; // Ejemplo: B001
      }

      // Función para generar un número de comprobante con formato
      function generateComprobanteNumber(type) {
        const randomNumber = Math.floor(Math.random() * 1000000); // Genera un número aleatorio de 7 dígitos
        return `${type}-${String(randomNumber).padStart(7, "0")}`; // Ejemplo: B001-0000123
      }

      // Inicializar los valores predeterminados al cargar la página (sin necesidad de presionar los botones)
      if (document.getElementById("boleta").checked) {
        numSerieInput.value = generateNumber("B");
        numComInput.value = generateComprobanteNumber("B");
      } else {
        numSerieInput.value = generateNumber("F");
        numComInput.value = generateComprobanteNumber("F");
      }

      // Maneja el cambio de tipo de comprobante (Boleta/Factura)
      tipoInputs.forEach((input) => {
        input.addEventListener("change", function () {
          if (this.value === "boleta") {
            numSerieInput.value = generateNumber("B");
            numComInput.value = generateComprobanteNumber("B");
          } else {
            numSerieInput.value = generateNumber("F");
            numComInput.value = generateComprobanteNumber("F");
          }
        });
      });
    });
  </script>

  <script>
    $(document).ready(function () {
      // Inicializa el DataTable solo una vez.
      if ($('#miTabla').DataTable().settings()[0]) {
        $('#miTabla').DataTable().destroy(); // Destruir si ya existe
      }
      $('#miTabla').DataTable(); // Inicializar el DataTable
    });

  </script>

  <script>
    $(document).ready(function () {
      $("#fecha").val(new Date().toISOString().split("T")[0]);
    });
  </script>

  <script>
    $(document).ready(function () {
      // Detecta cuando el usuario escribe en el campo de producto
      $("#producto").on("input", function () {
        var producto = $("#producto").val(); // Obtén el valor del campo 'producto'

        // Si hay algo escrito en el campo de producto
        if (producto) {
          // Establece los valores predeterminados para cantidad y descuento
          $("#cantidad").val(1); // Cantidad predeterminada 1
          $("#descuento").val(0); // Descuento predeterminado 0
        } else {
          // Si el campo está vacío, reseteamos la cantidad y el descuento
          $("#cantidad").val("");
          $("#descuento").val("");
        }
      });


      // Evento cuando se hace clic en el botón "AGREGAR"
      $("#agregarProducto").click(function () {
        var producto = $("#producto").val();
        var precio = $("#precio").val();
        var cantidad = $("#cantidad").val() || 1;
        var descuento = $("#descuento").val() || 0;

        // Verificar que los campos de producto y precio no estén vacíos
        if (!producto || !precio || isNaN(precio) || isNaN(cantidad)) {
          alert("Por favor, complete todos los campos (producto, precio).");
          return;
        }

        // Calcular el importe con la cantidad y el descuento
        var importe = parseFloat(precio) * parseInt(cantidad) - parseFloat(descuento);

        // Agregar la fila a la tabla
        var tabla = $("#miTabla").DataTable(); // Obtener la instancia de DataTable
        tabla.row.add([
          producto,
          precio,
          cantidad,
          descuento,
          importe.toFixed(2),
          '<button type="button" class="btn btn-danger btn-sm eliminarProducto">Eliminar</button>'
        ]).draw(false); // El método `draw(false)` asegura que no reinicie la tabla

        // Limpiar los campos de entrada después de agregar el producto
        $("#producto").val("");
        $("#precio").val("");
        $("#cantidad").val("");
        $("#descuento").val("");
      });



      // Función para eliminar producto de la tabla
      $(document).on("click", ".eliminarProducto", function () {
        $(this).closest("tr").remove();
      });
    });
  </script>

  <script>
    $("#finalizarBtn").click(function () {
    var cliente = $("#nomcliente").val(); // Nombre del cliente (opcional si usas el ID)
    var idcliente = $("#nomcliente").data('idcliente'); // Aquí obtenemos el ID del cliente
    var tipoComprobante = $('input[name="tipo"]:checked').val();
    var numSerie = $("#numserie").val();
    var numComprobante = $("#numcom").val();
    var fecha = $("#fecha").val();
    var moneda = $("select[name='tipomoneda']").val();

    // Verificar que todos los campos estén completos
    if (!idcliente || !tipoComprobante || !numSerie || !numComprobante || !fecha || !moneda) {
        alert("Por favor, complete todos los campos.");
        return;
    }

    var productos = [];
    $("#miTabla tbody tr").each(function () {
        var producto = {
            idproducto: $(this).find("td:eq(0)").text(), // ID del producto
            precioventa: parseFloat($(this).find("td:eq(1)").text()),
            cantidad: parseInt($(this).find("td:eq(2)").text()),
            descuento: parseFloat($(this).find("td:eq(3)").text()) || 0,
        };
        productos.push(producto);
    });

    // Verifica los datos de la venta antes de enviarlos
    console.log("Venta Data:", {
        cliente,
        tipoComprobante,
        numSerie,
        numComprobante,
        fecha,
        moneda,
        productos
    });

    if (productos.length === 0) {
        alert("Debe agregar al menos un producto.");
        return;
    }

    var ventaData = {
        idcliente: idcliente,  // Usamos el ID del cliente
        tipocom: tipoComprobante,
        numserie: numSerie,
        numcom: numComprobante,
        fechahora: fecha,
        moneda: moneda,
        productos: productos,
    };

    // Verifica el objeto de venta antes de enviarlo
    //console.log("Venta Data (JSON):", JSON.stringify(ventaData));

    $.ajax({
        url: "http://localhost/Fix360/app/controllers/Venta.controller.php",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(ventaData),
        success: function (response) {
            console.log("Respuesta del servidor:", response); // Imprime la respuesta del servidor
            if (response.status === "success") {
                alert("Venta registrada con éxito. ID de venta: " + response.idventa);
            } else {
                alert("Hubo un error al guardar la venta.");
            }
        },
        error: function (error) {
            console.error("Error en la solicitud AJAX:", error);
            alert("Hubo un error al guardar la venta.");
        },
    });

});

  </script>

  <!-- <script>
      $("#nomcliente").on("input", function () {
        var search = $(this).val();
    
        if (search.length > 2) {
          // Verifica que la ruta esté correcta
          $.getJSON("/fix360/app/models/buscar_cliente.php", { q: search })
            .done(function (data) {
              // Limpiar los resultados previos
              $("#clientesResultado").empty();
    
              // Mostrar los resultados
              if (data.length > 0) {
                data.forEach((cliente) => {
                  const li = document.createElement("li");
                  li.textContent = cliente.nombre; // Asegúrate de que el campo sea 'nombre' o el que corresponda
                  li.addEventListener("click", function () {
                    nomClienteInput.value = cliente.nombre; // Usar 'cliente.nombre' o el campo correcto
                    clientesResultado.innerHTML = ""; // Limpiar resultados
                  });
                  clientesResultado.appendChild(li);
                });
              } else {
                $("#clientesResultado").append(
                  "<li>No se encontraron resultados</li>"
                );
              }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
              console.error("Error en la solicitud:", textStatus, errorThrown);
            });
        } else {
          $("#clientesResultado").empty(); // Limpiar resultados si la búsqueda tiene menos de 2 caracteres
        }
      });
    </script> -->

  <!-- <script>
      $(document).ready(function () {
        $("#producto").on("input", function () {
          var search = $(this).val();
          if (search.length > 2) {
            $.getJSON(
              "../../app/models/buscar_producto.php",
              { search: search },
              function (data) {
                if (data && !data.error) {
                  var suggestions = data
                    .map(function (producto) {
                      return (
                        '<option value="' +
                        producto.nombre +
                        '" data-id="' +
                        producto.idproducto +
                        '" data-precio="' +
                        producto.precio +
                        '">'
                      ); // Agregamos el precio al option
                    })
                    .join("");
                  $("#producto").after(
                    '<datalist id="productosDataList">' +
                      suggestions +
                      "</datalist>"
                  );
                  $("#producto").attr("list", "productosDataList");
                }
              }
            );
          }
        });

        $("#producto").on("change", function () {
          var selected = $(this).find(":selected");
          var precio = selected.data("precio");
          var cantidad = 1; // Establecemos la cantidad a 1
          var descuento = 0; // Establecemos el descuento a 0

          $("#precio").val(precio);
          $("#cantidad").val(cantidad);
          $("#descuento").val(descuento);
        });
      });
    </script> -->

  <!-- <script>
        // Lógica para agregar productos dinámicamente a la tabla
        $(document).ready(function () {
          $("#agregarProducto").click(function () {
            const producto = $("input[name='producto[]']").last().val();
            const precio = $("input[name='precio[]']").last().val();
            const cantidad = $("input[name='cantidad[]']").last().val();
            const descuento = $("input[name='descuento[]']").last().val();
            
            if (producto && precio && cantidad) {
              const importe =
              parseFloat(precio) * parseInt(cantidad) -
              (parseFloat(descuento) || 0);
              
              $("#miTabla tbody").append(`
              <tr>
                <td>${producto}</td>
                <td>${precio}</td>
                <td>${cantidad}</td>
                <td>${descuento}</td>
                <td>${importe.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm eliminarProducto">Eliminar</button></td>
              </tr>
              `);
            }
          });
          
          $(document).on("click", ".eliminarProducto", function () {
            $(this).closest("tr").remove();
          });
        });
      </script> -->
  <!-- <script>
        document
          .getElementById("finalizarBtn")
          .addEventListener("click", function () {
            // Obtener los datos que necesitas enviar (ejemplo con variables)
            const tipo = "boleta"; // Tipo de venta
            const numserie = "V00001"; // Número de serie
            const numcomprobante = "0001"; // Número de comprobante
            const nomcliente = "Juan Pérez"; // Nombre del cliente
            const fecha = "2025-03-27"; // Fecha
            const tipomoneda = "S/.";
      
            const productos = JSON.stringify([1, 2, 3]); // Ejemplo de ID de productos
            const precios = JSON.stringify([100, 200, 150]); // Precios de los productos
            const cantidades = JSON.stringify([1, 2, 3]); // Cantidades de los productos
            const descuentos = JSON.stringify([0, 10, 5]); // Descuentos de los productos
      
            // Usar Fetch API para enviar los datos al servidor
            fetch("Venta.controller.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                tipo: tipo,
                numserie: numserie,
                numcomprobante: numcomprobante,
                nomcliente: nomcliente,
                fecha: fecha,
                tipomoneda: tipomoneda,
                productos: productos,
                precios: precios,
                cantidades: cantidades,
                descuentos: descuentos,
              }),
            })
              .then((response) => response.json())
              .then((data) => {
                // Aquí puedes manejar la respuesta del servidor
                if (data.success) {
                  alert("Venta finalizada con éxito!");
                } else {
                  alert("Hubo un error al finalizar la venta.");
                }
              })
              .catch((error) => {
                console.error("Error:", error);
                alert("Error al enviar la solicitud.");
              });
          });
      </script> -->
  <!-- <script>
      $(document).ready(function () {
        $('#agregarProducto').click(function () {
          var producto = $('#producto').val();
          var precio = $('#precio').val();
          var cantidad = $('#cantidad').val();
          var descuento = $('#descuento').val();
          
          if (producto && precio && cantidad) {
            var importe = (parseFloat(precio) * parseInt(cantidad)) - (parseFloat(descuento) || 0);
            $('#miTabla tbody').append(`
            <tr>
              <td>${producto}</td>
              <td>${precio}</td>
              <td>${cantidad}</td>
              <td>${descuento}</td>
              <td>${importe.toFixed(2)}</td>
              <td><button type="button" class="btn btn-danger btn-sm eliminarProducto">Eliminar</button></td>
            </tr>
            `);
          }
        });
        
        $(document).on('click', '.eliminarProducto', function () {
          $(this).closest('tr').remove();
        });
      });
    </script> -->
</body>

</html>