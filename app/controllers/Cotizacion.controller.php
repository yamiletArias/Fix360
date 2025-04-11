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
        echo json_encode(["status" => "error", "message" => "JSON invalido."]);
        exit;
      }
      $fechahora = Helper::limpiarCadena($dataJSON['fechahora'] ?? "");
      $vigenciadias = Helper::limpiarCadena($dataJSON['vigencia'] ?? "");
      if (empty($fechahora && $vigenciadias)) {
        $fechahora = date("Y-m-d H:i:s");
        $vigenciadias = date("Y-m-d");
      } elseif (strpos($fechahora, ' ') === false) {
        $fechahora .= " " . date("H:i:s");
      }

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
        "idcliente" => $idcliente,
        "productos" => $productos
      ]);
      if ($idCotInsertada > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Venta registrada con exito.",
            "idventa" => $idCotInsertada
        ]);
      } else {
        echo json_encode(["status" => "error", "message" => "No se pudo registrar la venta."]);
      }
      break;
  }
}

?>