document.getElementById("btnRegistrarProducto").addEventListener("click", function (e) {
    e.preventDefault();

    const form = document.getElementById("form-nuevo-producto");
    const formData = new FormData(form);

    fetch("http://localhost/fix360/app/controllers/producto.controller.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(resp => {
            if (resp.rows > 0) {
                showToast('Producto registrado exitosamente.', 'SUCCESS', 1500);

                // Obtener subcategoría y descripción para formar el nombre del producto
                const subcategoriaText = document.getElementById("subcategoria")
                    .options[document.getElementById("subcategoria").selectedIndex].text;
                const descripcion = document.getElementById("descripcion").value;
                const inputBusqueda = document.getElementById("producto");
                if (inputBusqueda) {
                    inputBusqueda.value = `${subcategoriaText} ${descripcion}`;
                }

                // **** Actualización clave: asignar el id retornado al objeto global selectedProduct ****
                // Se asume que la respuesta JSON ahora incluye la propiedad "idproducto" obtenida en PHP.
                selectedProduct = {
                    idproducto: resp.idproducto,  // Asigna el id obtenido por lastInsertId o parámetro OUT
                    subcategoria_producto: `${subcategoriaText} ${descripcion}`,
                    precio: document.getElementById("precio").value
                };

                // Cerrar el modal correctamente.
                const modalEl = document.getElementById('miModal');
                let modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (!modalInstance) {
                    modalInstance = new bootstrap.Modal(modalEl);
                }
                modalInstance.hide();

                // Eliminar backdrop manualmente.
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();

                // Quitar la clase modal-open y restaurar el scroll.
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';

                // Limpiar el formulario del modal
                form.reset();
            } else {
                showToast('Hubo un error al registrar el producto.', 'ERROR', 1500);
            }
        })
        .catch(err => {
            console.error("Error en la solicitud:", err);
            showToast('Error de conexión al registrar.', 'ERROR', 1500);
        });
});

document.addEventListener("DOMContentLoaded", function () {
    const marcaSelect = document.getElementById("marca");
    const categoriaSelect = document.getElementById("categoria");
    const subcategoriaSelect = document.getElementById("subcategoria");

    fetch("http://localhost/fix360/app/controllers/marca.controller.php?task=getAllMarcaProducto")
        .then(response => response.json())
        .then(data => {
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.idmarca;
                option.textContent = item.nombre;
                marcaSelect.appendChild(option);
            });
        })
        .catch(error => console.error("Error al cargar las marcas:", error));
    fetch("http://localhost/fix360/app/controllers/categoria.controller.php?task=getAll")
        .then(response => response.json())
        .then(data => {
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.idcategoria;
                option.textContent = item.categoria;
                categoriaSelect.appendChild(option);
            });
        })
        .catch(error => console.error("Error al cargar categorias:", error));

    function cargarSubcategorias() {
        const categoria = categoriaSelect.value;
        subcategoriaSelect.innerHTML = '<option value="">Seleccione una opcion</option>';
        if (categoria) {
            fetch(`http://localhost/fix360/app/controllers/subcategoria.controller.php?task=getSubcategoriaByCategoria&idcategoria=${encodeURIComponent(categoria)}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        const option = document.createElement("option");
                        option.value = item.idsubcategoria;
                        option.textContent = item.subcategoria;
                        subcategoriaSelect.appendChild(option);
                    });
                })
                .catch(error => console.error("Error al cargar subcategorias:", error));
        }
    }
    categoriaSelect.addEventListener("change", cargarSubcategorias);
});

document.addEventListener('DOMContentLoaded', function () {
    // Variables y elementos
    const proveedorSelect = document.getElementById('proveedor');
    const inputProductElement = document.getElementById("producto");
    const numSerieInput = document.getElementById("numserie");
    const numComInput = document.getElementById("numcom");
    const fechaInput = document.getElementById('fecha');
    const monedaSelect = document.getElementById('moneda');
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const agregarProductoBtn = document.querySelector("#agregarProducto");
    const tabla = document.querySelector("#tabla-detalle-compra tbody");
    const btnFinalizarCompra = document.getElementById('btnFinalizarCompra');

    // Nuevos elementos de input para los detalles del producto
    const inputPrecio = document.getElementById("precio");
    const inputCantidad = document.getElementById("cantidad");
    const inputDescuento = document.getElementById("descuento");

    let selectedProduct = {};
    const detalleCompra = [];

    function calcularTotales() {
        let totalImporte = 0;
        let totalDescuento = 0;

        document.querySelectorAll("#tabla-detalle-compra tbody tr").forEach(fila => {
            const subtotal = parseFloat(fila.querySelector("td:nth-child(6)").textContent) || 0;
            const descuento = parseFloat(fila.querySelector("td:nth-child(5)").textContent) || 0;
            totalImporte += subtotal;
            totalDescuento += descuento;
        });

        // Calcular IGV y Neto
        const igv = totalImporte - (totalImporte / 1.18);
        const neto = totalImporte / 1.18;
        document.getElementById("total").value = totalImporte.toFixed(2);
        document.getElementById("totalDescuento").value = totalDescuento.toFixed(2);
        document.getElementById("igv").value = igv.toFixed(2);
        document.getElementById("neto").value = neto.toFixed(2);
    }

    // Función para evitar duplicados en productos
    function estaDuplicado(idproducto = 0) {
        let duplicado = false;
        let i = 0;
        while (i < detalleCompra.length && !duplicado) {
            if (detalleCompra[i].idproducto == idproducto) {
                duplicado = true;
            }
            i++;
        }
        return duplicado;
    }

    // Manejador del botón "Agregar" para añadir producto al detalle de compra
    agregarProductoBtn.addEventListener("click", function () {
        const nomProducto = inputProductElement.value;
        const precioProducto = parseFloat(inputPrecio.value);
        const cantidadProducto = parseFloat(inputCantidad.value);
        const descuentoProducto = parseFloat(inputDescuento.value);

        if (!nomProducto || isNaN(precioProducto) || isNaN(cantidadProducto)) {
            alert("Por favor, complete todos los campos correctamente.");
            return;
        }

        const importe = (precioProducto * cantidadProducto) - descuentoProducto;
        const nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
        <td>${tabla.rows.length + 1}</td>
        <td>${nomProducto}</td>
        <td>${precioProducto.toFixed(2)}</td>
        <td>${cantidadProducto}</td>
        <td>${descuentoProducto.toFixed(2)}</td>
        <td>${importe.toFixed(2)}</td>
        <td><button class="btn btn-danger btn-sm">X</button></td>
      `;
        nuevaFila.querySelector("button").addEventListener("click", function () {
            nuevaFila.remove();
            actualizarNumeros();
            calcularTotales();
        });
        tabla.appendChild(nuevaFila);

        const detalle = {
            idproducto: selectedProduct.idproducto,
            producto: nomProducto,
            precio: precioProducto,
            cantidad: cantidadProducto,
            descuento: descuentoProducto,
            importe: importe.toFixed(2)
        };
        detalleCompra.push(detalle);

        inputProductElement.value = "";
        inputPrecio.value = "";
        inputCantidad.value = 1;
        inputDescuento.value = 0;

        calcularTotales();
    });

    function actualizarNumeros() {
        const filas = tabla.getElementsByTagName("tr");
        for (let i = 0; i < filas.length; i++) {
            filas[i].children[0].textContent = i + 1;
        }
    }

    // Función de debounce para evitar demasiadas llamadas en tiempo real
    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Función de navegación con el teclado para autocompletar
    function agregaNavegacion(input, itemsDiv) {
        let currentFocus = -1;
        input.addEventListener("keydown", function (e) {
            const items = itemsDiv.getElementsByTagName("div");
            if (e.key === "ArrowDown") {
                currentFocus++;
                addActive(items);
            } else if (e.key === "ArrowUp") {
                currentFocus--;
                addActive(items);
            } else if (e.key === "Enter") {
                e.preventDefault();
                if (currentFocus > -1 && items[currentFocus]) {
                    items[currentFocus].click();
                }
            }
        });

        function addActive(items) {
            if (!items) return false;
            removeActive(items);
            if (currentFocus >= items.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = items.length - 1;
            items[currentFocus].classList.add("autocomplete-active");
        }

        function removeActive(items) {
            for (let i = 0; i < items.length; i++) {
                items[i].classList.remove("autocomplete-active");
            }
        }
    }

    // Función para mostrar opciones de productos (autocompletado)
    function mostrarOpcionesProducto(input) {
        cerrarListas();
        if (!input.value) return;
        const searchTerm = input.value;
        fetch(`http://localhost/Fix360/app/controllers/Compra.controller.php?q=${searchTerm}&type=producto`)
            .then(response => response.json())
            .then(data => {
                const itemsDiv = document.createElement("div");
                itemsDiv.setAttribute("id", "autocomplete-list-producto");
                itemsDiv.setAttribute("class", "autocomplete-items");
                input.parentNode.appendChild(itemsDiv);
                if (data.length === 0) {
                    const noResultsDiv = document.createElement("div");
                    noResultsDiv.textContent = 'No se encontraron productos';
                    itemsDiv.appendChild(noResultsDiv);
                    return;
                }
                data.forEach(function (producto) {
                    const optionDiv = document.createElement("div");
                    optionDiv.textContent = producto.subcategoria_producto;
                    optionDiv.addEventListener("click", function () {
                        input.value = producto.subcategoria_producto;
                        inputPrecio.value = producto.precio;
                        inputCantidad.value = 1;
                        inputDescuento.value = 0;
                        selectedProduct = {
                            idproducto: producto.idproducto,
                            subcategoria_producto: producto.subcategoria_producto,
                            precio: producto.precio
                        };
                        cerrarListas();
                    });
                    itemsDiv.appendChild(optionDiv);
                });
                // Habilitar navegación por teclado en la lista de productos
                agregaNavegacion(input, itemsDiv);
            })
            .catch(err => console.error('Error al obtener los productos: ', err));
    }

    // Función para cerrar las listas de autocompletado
    function cerrarListas(elemento) {
        const items = document.getElementsByClassName("autocomplete-items");
        for (let i = 0; i < items.length; i++) {
            if (elemento !== items[i] && elemento !== inputProductElement) {
                items[i].parentNode.removeChild(items[i]);
            }
        }
    }

    // Listeners para el autocompletado de productos usando debounce
    const debouncedMostrarOpcionesProducto = debounce(mostrarOpcionesProducto, 500);
    inputProductElement.addEventListener("input", function () {
        debouncedMostrarOpcionesProducto(this);
    });
    inputProductElement.addEventListener("click", function () {
        debouncedMostrarOpcionesProducto(this);
    });
    document.addEventListener("click", function (e) {
        cerrarListas(e.target);
    });

    // Funciones para generar número de serie y de comprobante
    function generateNumber(type) {
        const randomNumber = Math.floor(Math.random() * 100);
        return `${type}${String(randomNumber).padStart(3, "0")}`;
    }
    function generateComprobanteNumber(type) {
        const randomNumber = Math.floor(Math.random() * 10000000);
        return `${type}-${String(randomNumber).padStart(7, "0")}`;
    }
    function inicializarCampos() {
        const tipoSeleccionado = document.querySelector('input[name="tipo"]:checked').value;
        if (tipoSeleccionado === "factura") {
            numSerieInput.value = generateNumber("F");
            numComInput.value = generateComprobanteNumber("F");
        } else {
            numSerieInput.value = generateNumber("B");
            numComInput.value = generateComprobanteNumber("B");
        }
    }
    inicializarCampos();
    tipoInputs.forEach((input) => {
        input.addEventListener("change", inicializarCampos);
    });
    // Establecer fecha actual
    const setFechaDefault = () => {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        fechaInput.value = `${year}-${month}-${day}`;
    };
    setFechaDefault();
    // Carga de proveedores vía AJAX
    fetch('http://localhost/Fix360/app/controllers/Compra.controller.php?type=proveedor')
        .then(response => response.json())
        .then(data => {
            proveedorSelect.innerHTML = '<option selected>Selecciona proveedor</option>';
            if (data.error) {
                console.error('Error:', data.error);
                return;
            }
            data.forEach(proveedor => {
                const option = document.createElement('option');
                option.value = proveedor.idproveedor;
                option.textContent = proveedor.nombre_empresa;
                proveedorSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar los proveedores:', error));

    // Navegación con Enter para ir de campo en campo (productos, precio, cantidad y descuento)
    inputProductElement.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            inputPrecio.focus();
        }
    });

    inputPrecio.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            inputCantidad.focus();
        }
    });

    inputCantidad.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            inputDescuento.focus();
        }
    });

    inputDescuento.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            // Opcional: puedes mover el foco al botón de agregar o ejecutar su acción directamente
            agregarProductoBtn.focus();
            // agregarProductoBtn.click();  // Si prefieres ejecutar la acción
        }
    });

    // Evento del botón "Guardar" para enviar la compra
    btnFinalizarCompra.addEventListener("click", function (e) {
        e.preventDefault();
        btnFinalizarCompra.disabled = true;
        btnFinalizarCompra.textContent = "Guardando...";
        numSerieInput.disabled = false;
        numComInput.disabled = false;

        if (proveedorSelect.value === "" || proveedorSelect.value === "Selecciona proveedor") {
            alert("Por favor, selecciona un proveedor.");
            btnFinalizarCompra.disabled = false;
            btnFinalizarCompra.textContent = "Guardar";
            return;
        }
        if (detalleCompra.length === 0) {
            alert("Por favor, agrega al menos un producto.");
            btnFinalizarCompra.disabled = false;
            btnFinalizarCompra.textContent = "Guardar";
            return;
        }
        const dataCompra = {
            tipocom: document.querySelector('input[name="tipo"]:checked').value,
            fechacompra: fechaInput.value.trim(),
            numserie: numSerieInput.value.trim(),
            numcom: numComInput.value.trim(),
            moneda: monedaSelect.value,
            idproveedor: proveedorSelect.value,
            productos: detalleCompra
        };

        fetch("http://localhost/Fix360/app/controllers/Compra.controller.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dataCompra)
        })
            .then(response => response.text())
            .then(text => {
                console.log("Respuesta del servidor:", text);
                try {
                    const json = JSON.parse(text);
                    if (json && json.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Compra registrada con éxito!',
                            showConfirmButton: false,
                            timer: 1800
                        }).then(() => {
                            window.location.href = 'listar-compras.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al registrar la venta',
                            text: 'Inténtalo nuevamente.',
                        });
                    }
                } catch (e) {
                    console.error("No se pudo parsear JSON:", e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Respuesta inesperada',
                        text: 'El servidor no devolvió una respuesta válida.',
                    });
                }
            })
            .finally(() => {
                btnFinalizarCompra.disabled = false;
                btnFinalizarCompra.textContent = "Guardar";
            });
    });
});