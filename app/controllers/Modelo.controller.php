<?php

if (isset($_SERVER['REQUEST_METHOD'])) {

  header('Content-type: application/json; charset=utf-8');

  require_once "../models/Modelo.php";
  $modelo = new Modelo();

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      // Se esperan los parámetros idtipov e idmarca en la URL
      if (isset($_GET['idtipov']) && isset($_GET['idmarca'])) {
        $params = [
          "idtipov" =>  $_GET['idtipov'],
          "idmarca" =>  $_GET['idmarca']
        ];

        echo json_encode($modelo->GetAllModelosByTipoMarca($params));
      } else {
        echo json_encode([
          "status"  => false,
          "message" => "Faltan parámetros necesarios: idtipov e idmarca."
        ]);
      }
      break;


    case 'POST':
      // esperamos JSON { "idtipov": 1, "idmarca": 2, "modelo": "MiModelo" }
      $data = json_decode(file_get_contents('php://input'), true);
      if (!empty($data['idtipov']) && !empty($data['idmarca']) && !empty($data['modelo'])) {
        $newId = $modelo->registerModelo($data);
        if ($newId > 0) {
          echo json_encode(['success' => true, 'idmodelo' => $newId]);
        } else {
          http_response_code(500);
          echo json_encode(['success' => false, 'message' => 'No se pudo registrar el modelo.']);
        }
      } else {
        http_response_code(400);
        echo json_encode([
          'success' => false,
          'message' => 'Faltan parámetros: idtipov, idmarca y modelo.'
        ]);
      }
      break;

    default:

      break;
  }
}
