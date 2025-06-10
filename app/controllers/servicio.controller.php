<?php
header('Content-Type: application/json; charset=utf-8');
require_once "../models/Servicio.php";
require_once "../helpers/Helper.php";  // para limpiar cadenas

$servicioModel = new Servicio();

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    // GET /ServicioController.php?task=getServicioBySubcategoria&idsubcategoria=5
    if (isset($_GET['task']) && $_GET['task'] === 'getServicioBySubcategoria') {
      $idsubcategoria = intval($_GET['idsubcategoria'] ?? 0);
      echo json_encode($servicioModel->getServicioBySubcategoria($idsubcategoria));
      exit;
    }
    echo json_encode(["error" => "Parámetros GET inválidos"]);
    exit;
        // GET /ServicioController.php?task=getServiciosMensuales
    if (isset($_GET['task']) && $_GET['task'] === 'getServiciosMensuales') {
      $data = $servicioModel->getServiciosMensuales();
      echo json_encode([
        'status' => 'success',
        'data'   => $data
      ]);
      exit;
    }


  case 'POST':
    // Leer el JSON crudo
    $input = file_get_contents('php://input');
    $dataJSON = json_decode($input, true);
    if ($dataJSON === null) {
      echo json_encode(["error" => "JSON inválido"]);
      exit;
    }

    // Sólo soportamos: { task: "registerServicio", idsubcategoria: X, servicio: "texto" }
    if (isset($dataJSON['task']) && $dataJSON['task'] === 'registerServicio') {
      // Limpiar y validar
      $idsubcategoria = intval($dataJSON['idsubcategoria'] ?? 0);
      $servicioNombre = Helper::limpiarCadena($dataJSON['servicio'] ?? '');

      if ($idsubcategoria <= 0 || $servicioNombre === '') {
        echo json_encode(["error" => "Faltan datos para registrar el servicio"]);
        exit;
      }

      // Llamamos al model y obtenemos el ID recién creado
      $resultado = $servicioModel->registerServicio([
        "idsubcategoria" => $idsubcategoria,
        "servicio"       => $servicioNombre
      ]);

      if ($resultado['idservicio'] > 0) {
        echo json_encode([
          "success"      => true,
          "idservicio"   => $resultado['idservicio'],
          "servicio"     => $resultado['servicio']
        ]);
      } else {
        echo json_encode(["error" => "No se pudo registrar el servicio"]);
      }
      exit;
    }

    echo json_encode(["error" => "Operación POST no válida"]);
    exit;

  default:
    echo json_encode(["error" => "Método no soportado"]);
    exit;
}
?>
