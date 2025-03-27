<?php
require_once __DIR__ ."/../models/Contactabilidad.php";
session_start();
header("Content-Type: application/json");

$contactabilidad = new Contactabilidad();

try {
    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
            if (!isset($_POST['operation']) || empty(trim($_POST['operation']))) {
                echo json_encode(["error" => "Operacion no especificada"]);
                exit;
            }

            switch ($_POST["operation"]) {
                case 'getContactabilidad':
                    echo json_encode($contactabilidad->getContactabilidad());
                    break;

                default:
                    echo json_encode(["error" => "Operación no válida"]);
                    break;
            }
            break; // Agregado para que no siga ejecutando el código

        default:
            echo json_encode(["error" => "Método no soportado"]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
