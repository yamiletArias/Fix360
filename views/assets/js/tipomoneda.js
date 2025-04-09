    // Función para cargar las monedas
    function cargarMonedas() {
        fetch('http://localhost/Fix360/app/controllers/Venta.controller.php?type=moneda')
          .then(response => response.json())
          .then(data => {
            const selectMoneda = document.getElementById("moneda");
            data.forEach(item => {
              // si el valor es SOLES ya existe saltarse
              if (item.moneda !== "Soles") {
                const option = document.createElement("option");
                option.value = item.moneda;
                option.textContent = item.moneda;
                selectMoneda.appendChild(option);
              }
            });
          })
          .catch(error => {
            console.error('Error al cargar las monedas:', error);
          });
      }
      cargarMonedas();