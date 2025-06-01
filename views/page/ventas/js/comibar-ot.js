// --- Funciones para generar Serie y Número de Comprobante ---
function generateNumber(prefix) {
  // Tres dígitos aleatorios entre 000 y 099 (para dar formato “B012”, “F045”, etc.)
  return `${prefix}${String(Math.floor(Math.random() * 100)).padStart(3, "0")}`;
}

function generateComprobanteNumber(prefix) {
  // Siete dígitos aleatorios entre 0000000 y 9999999, con guión al inicio del número
  return `${prefix}-${String(Math.floor(Math.random() * 1e7)).padStart(7, "0")}`;
}

// --- Función para inicializar Serie y Número de Comprobante en el modal ---
function inicializarCombinarOT() {
  // Obtenemos el tipo seleccionado (boleta o factura)
  const comboTipocom = document.getElementById("comboTipocom");
  const tipo = comboTipocom.value;

  let prefijoSerie    = "";
  let prefijoComprobante = "";

  switch (tipo) {
    case "factura":
      prefijoSerie       = "F";
      prefijoComprobante = "F";
      break;
    case "boleta":
      prefijoSerie       = "B";
      prefijoComprobante = "B";
      break;
    default:
      // (No contemplamos “orden de trabajo” acá, porque la modal solo ofrece boleta/factura)
      prefijoSerie       = "";
      prefijoComprobante = "";
      break;
  }

  // Asignamos los valores generados a los inputs correspondientes
  const numSerieInput  = document.getElementById("numSerieCombinar");
  const numComInput    = document.getElementById("numComCombinar");

  // Generamos nuevo número de serie y comprobante
  numSerieInput.value = generateNumber(prefijoSerie);
  numComInput.value   = generateComprobanteNumber(prefijoComprobante);
}

// --- Vincular el evento al cambiar el select ---
document.addEventListener("DOMContentLoaded", () => {
  const comboTipocom = document.getElementById("comboTipocom");
  if (comboTipocom) {
    // Cada vez que cambie “boleta” <-> “factura”, regeneramos números
    comboTipocom.addEventListener("change", inicializarCombinarOT);
    // Al abrir la modal por primera vez, forzamos la generación inicial
    inicializarCombinarOT();
  }

  // Opcional: si usas Bootstrap, puedes resetear los valores al mostrar el modal
  const modalCombinar = document.getElementById("modalCombinarOT");
  if (modalCombinar) {
    modalCombinar.addEventListener("show.bs.modal", () => {
      // Re‐inicializamos número de serie y comprobante cada vez que se abra el modal
      inicializarCombinarOT();
    });
  }
});
