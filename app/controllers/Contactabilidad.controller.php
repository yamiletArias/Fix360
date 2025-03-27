<?php
session_start();
require_once "../models/Contactabilidad.php";
header("Content-Type: application/json");

$contactabilidad = new Contactabilidad();

try {
    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
            if (isset($_POST['operation'])) {
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

        default:
            echo json_encode(["error" => "Método no soportado"]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
