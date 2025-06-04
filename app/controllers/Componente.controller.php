<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
  header('Content-type: application/json; charset=utf-8');

  require_once "../models/Componente.php";
  require_once "../helpers/helper.php";

  $componente = new Componente();

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      if ($_GET['task'] == 'getAll') {
        echo json_encode($componente->getAll());
      }

      break;

    case 'POST':
      $input = file_get_contents('php://input');

      $dataJSON = json_decode($input, true);

      if ($dataJSON === null) {
        echo json_encode(["error" => "JSON inválido"]);
        error_log("JSON Recibido: " . $input);
        exit;
      }

      $registro = [
        "componente" => Helper::limpiarCadena($dataJSON["componente"])
      ];
      $n = $componente->add($registro);

      // componente.controller.php → método add()
if ($n['idcomponente'] === 0) {
    echo json_encode(["error" => "No se pudo registrar el componente"]);
} else {
    echo json_encode([
      "success"      => "Componente registrado",
      "idcomponente" => $n['idcomponente'],
      "componente"   => $n['componente']
    ]);
}

      break;
    default:
      # code...
      break;
  }
}
