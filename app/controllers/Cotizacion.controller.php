<?php
if (isset($_SERVER['REQUEST_METHOD'])) {

  header('Content-Type: application/json; charset=utf-8');
  require_once __DIR__ . '/../models/sesion.php';
  require_once "../models/Cotizacion.php";
  require_once "../helpers/helper.php";

  $cotizacion = new Cotizacion();

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      $tipo = Helper::limpiarCadena($_GET['type'] ?? "");
      // 1) Listar por periodo si vienen modo+fecha
      if (isset($_GET['modo'], $_GET['fecha'])) {
        $modo = in_array($_GET['modo'], ['dia', 'semana', 'mes'], true)
          ? $_GET['modo']
          : 'dia';
        $fecha = $_GET['fecha'] ?: date('Y-m-d');
        $cotizaciones = $cotizacion->listarPorPeriodoCotizacion($modo, $fecha);
        echo json_encode(['status' => 'success', 'data' => $cotizaciones]);
        exit;
      }

      // 4) cotizaciones eliminadas
      if (isset($_GET['action']) && $_GET['action'] === 'cotizaciones_eliminadas') {
        $eliminadas = $cotizacion->getCotizacionesEliminadas();
        echo json_encode(['status' => 'success', 'data' => $eliminadas]);
        exit;
      }

      // 5) Justificación de eliminación
      if (
        isset($_GET['action'], $_GET['idcotizacion'])
        && $_GET['action'] === 'justificacion'
      ) {
        $id = (int) $_GET['idcotizacion'];
        try {
          $just = $cotizacion->getJustificacion($id);
          if ($just !== null) {
            echo json_encode(['status' => 'success', 'justificacion' => $just]);
          } else {
            echo json_encode(['status' => 'error', 'message' => 'No existe justificación']);
          }
        } catch (Exception $e) {
          echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
      }

      // 2) Monedas
      if (isset($_GET['type']) && $_GET['type'] === 'moneda') {
        $monedas = $cotizacion->getMonedasVentas();
        echo json_encode(['status' => 'success', 'data' => $monedas]);
        exit;
      }

      // 5) búsqueda dinámica
      if (isset($_GET['q']) && $_GET['q'] !== '') {
        $term = Helper::limpiarCadena($_GET['q']);
        if ($tipo === 'producto') {
          $res = $cotizacion->buscarProducto($term);
        } else {
          $res = $cotizacion->buscarCliente($term);
        }
        echo json_encode(['status' => 'success', 'data' => $res]);
        exit;
      }

      /* if ($_GET['task'] === 'getClienteCotizacion' && isset($_GET['idcotizacion'])) {
        $id = intval($_GET['idcotizacion']);
        $stmt = $pdo->prepare("
            SELECT cliente 
            FROM vista_detalle_cotizacion 
            WHERE idcotizacion = :id 
            LIMIT 1
          ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode(['cliente' => $row['cliente'] ?? null]);
        exit;
      } */

      // 4) Fallback: todas las cotizaciones activas
      echo json_encode(['status' => 'success', 'data' => $cotizacion->getAll()]);
      exit;

    case 'POST':

      // Anulación de venta (soft-delete) con justificación
      if (isset($_POST['action'], $_POST['idcotizacion']) && $_POST['action'] === 'eliminar') {
        $id = intval($_POST['idcotizacion']);
        $justificacion = trim($_POST['justificacion'] ?? "");

        error_log("Intentando anular compra #$id. Justificación: $justificacion");

        $ok = $venta->deleteVenta($id, $justificacion);
        error_log("Resultado deleteVenta: " . ($ok ? 'OK' : 'FAIL'));

        echo json_encode([
          'status' => $ok ? 'success' : 'error',
          'message' => $ok ? 'Compra anulada.' : 'No se pudo anular la compra.'
        ]);
        exit;
      }

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
      $idadmin = $_SESSION['login']['idcolaborador'] ?? 0;
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
        "idcolaborador" => $idadmin,
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