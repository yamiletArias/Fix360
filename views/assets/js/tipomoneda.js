function cargarMonedas() {
  fetch('http://localhost/Fix360/app/controllers/Venta.controller.php?type=moneda')
    .then(res => res.json())
    .then(data => {
      const selectMoneda = document.getElementById("moneda");

      data.forEach(item => {
        // limpiamos espacios y unificamos mayúsculas/minúsculas
        const m = item.moneda.trim();
        // si ya existe esa opción, no la volvemos a crear
        if (!selectMoneda.querySelector(`option[value="${m}"]`)) {
          const opt = document.createElement("option");
          opt.value = m;
          opt.textContent = m;
          selectMoneda.appendChild(opt);
        }
      });

      // por si acaso reafirmamos “Soles” como opción seleccionada
      const defaultOpt = selectMoneda.querySelector('option[value="Soles"]');
      if (defaultOpt) defaultOpt.selected = true;
    })
    .catch(err => console.error('Error al cargar las monedas:', err));
}

cargarMonedas();
