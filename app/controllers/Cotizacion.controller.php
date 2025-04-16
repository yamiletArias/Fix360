<?php
if (isset($_SERVER['REQUEST_METHOD'])) {

  header('Content-Type: aplication/json; charset=utf-8');
  require_once "../models/Cotizacion.php";
  require_once "../helpers/helper.php";

  $cotizacion = new Cotizacion();

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      $tipo = Helper::limpiarCadena($dataJSON['tipo'] ?? "");
      if (isset($_GET['type']) && $_GET['type'] == 'moneda') {
        echo json_encode($cotizacion->getMonedasVentas());
      } else if (isset($_GET['q']) && !empty($_GET['q'])) {
        if (isset($_GET['type']) && $_GET['type'] == 'producto') {
          // Buscar productos
          $termino = $_GET['q'];
          echo json_encode($cotizacion->buscarProducto($termino));
        } else {
          // Buscar clientes
          $termino = $_GET['q'];
          echo json_encode($cotizacion->buscarCliente($termino));
        }
      } else {
        echo json_encode($cotizacion->getAll());
      }
      break;

    case 'POST':
      //captura el json de entrada
      $input = file_get_contents('php://input');
      error_log("Entrada POST: " . $input);

      $dataJSON = json_decode($input, true);
      if (!$dataJSON) {
        error_log("Error: JSON inválido.");
        echo json_encode(["status" => "error", "message" => "JSON inválido."]);
        exit;
      } 

      //limpiar y validacion de datos
      $fechahora = Helper::limpiarCadena($dataJSON['fechahora'] ?? "");
      $vigenciaInput = Helper::limpiarCadena($dataJSON['vigenciadias'] ?? "");

      // Si el valor de vigenciadias es una fecha (contiene "-"), calculamos la diferencia en días.
if (strpos($vigenciaInput, "-") !== false) {
  try {
      $fechaVigencia = new DateTime($vigenciaInput);
      // Usamos $fechahora para la fecha de cotización, o la fecha actual si no se definió
      $fechaCotizacion = !empty($fechahora)
          ? new DateTime($fechahora)
          : new DateTime();
      $intervalo = $fechaCotizacion->diff($fechaVigencia);
      $vigenciadias = $intervalo->days;
  } catch (Exception $e) {
      error_log("Error al convertir la fecha de vigencia: " . $e->getMessage());
      $vigenciadias = 0;
  }
} else {
  // Si ya es un número (por ejemplo, enviado desde JavaScript), se usa directamente
  $vigenciadias = intval($vigenciaInput);
}

// Si $fechahora está vacío, asignamos la fecha y hora actual
if (empty($fechahora)) {
  $fechahora = date("Y-m-d H:i:s");
} elseif (strpos($fechahora, ' ') === false) {
  $fechahora .= " " . date("H:i:s");
}

      $moneda = Helper::limpiarCadena($dataJSON['moneda'] ?? "");
      $idcliente = $dataJSON['idcliente'] ?? 0;
      $productos = $dataJSON['productos'] ?? [];

      if (empty($productos)) {
        echo json_encode(["status" => "error", "message" => "No se enviaron productos."]);
        exit;
      }
      error_log("Datos recibidos: " . print_r($dataJSON, true));

      $cotizacion = new Cotizacion();
      $idCotInsertada = $cotizacion->registerCotizacion([
        "fechahora" => $fechahora,
        "vigenciadias" => $vigenciadias,
        "moneda" => $moneda,
        "idcliente" => $idcliente,
        "productos" => $productos
      ]);
      if ($idCotInsertada > 0) {
        echo json_encode([
          "status" => "success",
          "message" => "Venta registrada con exito.",
          "idcotizacion" => $idCotInsertada
        ]);
      } else {
        echo json_encode(["status" => "error", "message" => "No se pudo registrar la venta."]);
      }
      break;
  }
}

?>