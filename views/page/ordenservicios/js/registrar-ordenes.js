
    function actualizarOpciones() {

        const select = document.getElementById("selectMetodo");
        const personaSeleccionada = document.getElementById("rbtnpersona").checked;
        // Limpiar opciones actuales
        select.innerHTML = "";
        if (personaSeleccionada) {
            select.innerHTML += `<option value="dni">DNI</option>`;
            select.innerHTML += `<option value="nombre">Nombre</option>`;
        } else {
            select.innerHTML += `<option value="ruc">RUC</option>`;
            select.innerHTML += `<option value="razonsocial">Razón Social</option>`;
        }
    }

    // Función para buscar propietarios en el modal y llenar la tabla de resultados
    function buscarPropietario() {
        const tipo = document.getElementById("rbtnpersona").checked ? "persona" : "empresa";
        const metodo = document.getElementById("selectMetodo").value;
        const valor = document.getElementById("vbuscado").value.trim();

        // Si no se ingresa valor, limpia la tabla
        if (valor === "") {
            document.querySelector("#tabla-resultado tbody").innerHTML = "";
            return;
        }

        // Construir la URL de la consulta (ajusta la ruta según tu estructura)
        const url = `http://localhost/fix360/app/controllers/Propietario.controller.php?tipo=${encodeURIComponent(tipo)}&metodo=${encodeURIComponent(metodo)}&valor=${encodeURIComponent(valor)}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector("#tabla-resultado tbody");
                tbody.innerHTML = "";
                // Crear filas para cada resultado
                data.forEach((item, index) => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
          <td>${index + 1}</td>
          <td>${item.nombre}</td>
          <td>${item.documento}</td>
          <td>
            <button type="button" class="btn btn-success btn-sm btn-confirmar" data-id="${item.idcliente}" data-bs-dismiss="modal">
            <i class="fa-solid fa-circle-check"></i>
            </button>
          </td>
        `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => console.error("Error en búsqueda:", error));
    }

    // Cuando se hace clic en el botón "Confirmar" del modal
    document.querySelector("#tabla-resultado").addEventListener("click", function (e) {
        if (e.target.closest(".btn-confirmar")) {
            const btn = e.target.closest(".btn-confirmar");
            const idcliente = btn.getAttribute("data-id");

            // Obtener el nombre desde la fila (segunda columna)
            const fila = btn.closest("tr");
            const nombre = fila.cells[1].textContent;

            // Guardar el id y el nombre en los inputs correspondientes
            document.getElementById("hiddenIdCliente").value = idcliente;
            document.getElementById("propietario").value = nombre;

            // Cerrar el modal después de un pequeño delay
            setTimeout(() => {
                const modalEl = document.getElementById("miModal");
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.hide();
            }, 100);
        }
    });

    // Escuchar el input de búsqueda para disparar la consulta (puedes agregar debounce para evitar llamadas excesivas)
    document.getElementById("vbuscado").addEventListener("keyup", buscarPropietario);

    // Actualizar opciones del select y disparar búsqueda al cambiar los radio buttons
    document.getElementById("rbtnpersona").addEventListener("click", function () {
        actualizarOpciones();
        buscarPropietario();
    });
    document.getElementById("rbtnempresa").addEventListener("click", function () {
        actualizarOpciones();
        buscarPropietario();
    });

    // Inicializar las opciones del select al cargar el modal
    document.addEventListener("DOMContentLoaded", actualizarOpciones);
    const fechaInput = document.getElementById('fechaIngreso');
    const setFechaDefault = () => {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        fechaInput.value = `${year}-${month}-${day}`;
    };
    setFechaDefault();

    // Ejecutar la función al cargar la página para establecer las opciones iniciales
    actualizarOpciones();

    document.addEventListener("DOMContentLoaded", function () {
        // Obtener los elementos del DOM
        const tiposervicioSelect = document.getElementById("subcategoria");
        const servicioSelect = document.getElementById("servicio");
        const mecanicoSelect = document.getElementById("mecanico");
        const vehiculoSelect = document.getElementById("vehiculo");
        const hiddenIdCliente = document.getElementById("hiddenIdCliente");

        // Cargar subcategorías de servicios
        fetch("http://localhost/fix360/app/controllers/subcategoria.controller.php?task=getServicioSubcategoria")
            .then(response => response.json())
            .then(data => {
                data.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item.idsubcategoria;
                    option.textContent = item.subcategoria;
                    tiposervicioSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los tipo de servicio:", error));

        // Cargar mecánicos
        fetch("http://localhost/fix360/app/controllers/mecanico.controller.php?task=getAllMecanico")
            .then(response => response.json())
            .then(data => {
                data.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item.idcolaborador;
                    option.textContent = item.nombres;
                    mecanicoSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar mecanico:", error));

        // Función para cargar servicios en función de la subcategoría seleccionada
        function cargarServicio() {
            const tiposervicio = tiposervicioSelect.value;
            servicioSelect.innerHTML = '<option value="">Seleccione una opción</option>';

            if (tiposervicio) {
                fetch(`http://localhost/fix360/app/controllers/servicio.controller.php?task=getServicioBySubcategoria&idsubcategoria=${encodeURIComponent(tiposervicio)}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            const option = document.createElement("option");
                            option.value = item.idservicio;
                            option.textContent = item.servicio;
                            option.dataset.precio = item.precio;
                            servicioSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Error al cargar los servicios:", error));
            }
        }
        tiposervicioSelect.addEventListener("change", cargarServicio);

        // Función para cargar los vehículos asociados al cliente
        function cargarVehiculos() {
            const idcliente = hiddenIdCliente.value;
            vehiculoSelect.innerHTML = '<option value="">Seleccione una opción</option>';

            if (idcliente) {
                fetch(`http://localhost/fix360/app/controllers/vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${encodeURIComponent(idcliente)}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            const option = document.createElement("option");
                            option.value = item.idvehiculo;
                            option.textContent = item.vehiculo;
                            vehiculoSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Error al cargar los vehiculos:", error));
            }
        }
        // Escuchar el evento 'change' en el input oculto
        hiddenIdCliente.addEventListener("change", cargarVehiculos);

        // Función para confirmar la selección del propietario en el modal.
        // Se asume que en la tabla del modal existe un botón con la clase .btn-confirmar
        document.querySelector("#tabla-resultado").addEventListener("click", function (e) {
            if (e.target.closest(".btn-confirmar")) {
                const btn = e.target.closest(".btn-confirmar");
                const idcliente = btn.getAttribute("data-id");

                // Obtener el nombre desde la fila (segunda columna)
                const fila = btn.closest("tr");
                const nombre = fila.cells[1].textContent;

                // Actualizar el input oculto y el input visible de propietario
                hiddenIdCliente.value = idcliente;
                document.getElementById("propietario").value = nombre;

                // Disparar manualmente el evento 'change' para cargar los vehículos
                hiddenIdCliente.dispatchEvent(new Event('change'));

                // Cerrar el modal después de un pequeño delay
                setTimeout(() => {
                    const modalEl = document.getElementById("miModal");
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();
                }, 100);
            }
        });

        // Ejemplo de otras funciones o inicializaciones (por ejemplo, para el input de fecha)
        const today = new Date();

        function formatDate(date) {
            const yyyy = date.getFullYear();
            const mm = date.getMonth() + 1; // Los meses inician en 0
            const dd = date.getDate();
            return `${yyyy}-${mm < 10 ? '0' + mm : mm}-${dd < 10 ? '0' + dd : dd}`;
        }
        const currentDate = formatDate(today);
        const twoDaysAgo = new Date();
        twoDaysAgo.setDate(today.getDate() - 2);
        const minDate = formatDate(twoDaysAgo);
        const dateInput = document.getElementById('fechaIngreso');
        dateInput.setAttribute('min', minDate);
        dateInput.setAttribute('max', currentDate);

        // Inicialización de opciones en el modal para búsqueda (opcional)
        function actualizarOpciones() {
            const select = document.getElementById("selectMetodo");
            const personaSeleccionada = document.getElementById("rbtnpersona").checked;
            select.innerHTML = "";
            if (personaSeleccionada) {
                select.innerHTML += `<option value="dni">DNI</option>`;
                select.innerHTML += `<option value="nombre">Nombre</option>`;
            } else {
                select.innerHTML += `<option value="ruc">RUC</option>`;
                select.innerHTML += `<option value="razonsocial">Razón Social</option>`;
            }
        }
        document.addEventListener("DOMContentLoaded", actualizarOpciones);
    });

    document.addEventListener('DOMContentLoaded', () => {
        const btnAgregar   = document.querySelector('#button-addon2.btn-success');
        const btnAceptar   = document.querySelector('button.btn-success.text-end');
        const tbody        = document.querySelector('#tabla-detalle tbody');
        const detalleArr   = [];
        const selectServ   = document.getElementById('servicio');
        const selectMec    = document.getElementById('mecanico');
        const selectVeh    = document.getElementById('vehiculo');
        const kmInput      = document.getElementById('kilometraje');
        const fechaIn      = document.getElementById('fechaIngreso');
        const hidCliente   = document.getElementById('hiddenIdCliente');
        const obsInput     = document.getElementById('observaciones'); // si lo agregas al form
        const ingGruaChk   = document.getElementById('ingresogrua');   // idem
      
        function recalcular() {
          let sub = detalleArr.reduce((s, i) => s + i.precio, 0);
          let igv = sub - sub/1.18;
          let net = sub/1.18;
          document.getElementById('subtotal').value = sub.toFixed(2);
          document.getElementById('igv').value      = igv.toFixed(2);
          document.getElementById('neto').value     = net.toFixed(2);
        }
      
        btnAgregar.addEventListener('click', () => {
          const idserv = +selectServ.value;
          const srvTxt = selectServ.selectedOptions[0].textContent;
          const precio = parseFloat(selectServ.selectedOptions[0].dataset.precio || 0);
          const mecId  = +selectMec.value;
          const mecTxt = selectMec.selectedOptions[0].textContent;
      
          if (!idserv || !mecId) return alert('Selecciona servicio y mecánico');
      
          detalleArr.push({ idservicio: idserv, precio });
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${tbody.children.length+1}</td>
            <td>${srvTxt}</td>
            <td>${mecTxt}</td>
            <td>${precio.toFixed(2)}</td>
            <td><button class="btn btn-sm btn-danger">X</button></td>
          `;
          tr.querySelector('button').onclick = () => {
            const idx = Array.from(tbody.children).indexOf(tr);
            detalleArr.splice(idx, 1);
            tr.remove();
            recalcular();
          };
          tbody.appendChild(tr);
          recalcular();
        });
      
        btnAceptar.addEventListener('click', e => {
          e.preventDefault();
          if (detalleArr.length === 0) {
            return alert('Agrega al menos un servicio');
          }
          const payload = {
            idmecanico:    +selectMec.value,
            idpropietario:+hidCliente.value,
            idcliente:    +hidCliente.value,
            idvehiculo:   +selectVeh.value,
            kilometraje:  parseFloat(kmInput.value),
            observaciones: obsInput?.value || '',
            ingresogrua:  ingGruaChk?.checked || false,
            fechaingreso: fechaIn.value,
            fecharecordatorio: null,
            detalle:      detalleArr
          };
      
          fetch("http://localhost/fix360/app/controllers/OrdenServicio.controller.php", {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(payload)
          })
          .then(r => r.json())
          .then(js => {
            if (js.status==='success') {
                showToast('Orden registrada exitosamente', 'SUCCESS', 1000);
            } else {
                showToast('Error al registrar el orden de servicio', 'ERROR', 1500);
            }
          });
        });
      });
      
