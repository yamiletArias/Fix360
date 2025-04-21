function cargarMonedas() {
  fetch('http://localhost/Fix360/app/controllers/Venta.controller.php?type=moneda')
    .then(response => response.json())
    .then(data => {
      const selectMoneda = document.getElementById("moneda");

      // Limpiar primero para evitar duplicados si se vuelve a llamar
      selectMoneda.innerHTML = '<option value="">Seleccione moneda</option>';

      // Crear un Set para evitar monedas duplicadas
      const monedasAgregadas = new Set();

      data.forEach(item => {
        const moneda = item.moneda?.trim(); // por si acaso

        if (moneda && !monedasAgregadas.has(moneda) && moneda !== "Soles") {
          const option = document.createElement("option");
          option.value = moneda;
          option.textContent = moneda;
          selectMoneda.appendChild(option);

          monedasAgregadas.add(moneda);
        }
      });
    })
    .catch(error => {
      console.error('Error al cargar las monedas:', error);
    });
}

cargarMonedas();
