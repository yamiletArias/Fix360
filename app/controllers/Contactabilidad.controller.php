<?php
require_once __DIR__ . "/../models/Contactabilidad.php";
session_start();
header("Content-Type: application/json");

$contactabilidadModel = new Contactabilidad();

try {
    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
            if (!isset($_POST['operation']) || empty(trim($_POST['operation']))) {
                echo json_encode(["error" => "Operación no especificada"]);
                exit;
            }

            switch ($_POST["operation"]) {
                case 'getContactabilidad':
                    $result = $contactabilidadModel->getContactabilidad();
                    echo json_encode($result);
                    break;

                case 'getGraficoContactabilidad':
                    if (
                        !isset($_POST['periodo'])    || 
                        !isset($_POST['fecha_desde']) || 
                        !isset($_POST['fecha_hasta'])
                    ) {
                        echo json_encode(["error" => "Faltan parámetros (periodo, fecha_desde, fecha_hasta)"]);
                        exit;
                    }

                    $periodo    = trim($_POST['periodo']);
                    $fechaDesde = trim($_POST['fecha_desde']);
                    $fechaHasta = trim($_POST['fecha_hasta']);

                    $validPeriods = ['ANUAL','MENSUAL','SEMANAL'];
                    if (!in_array($periodo, $validPeriods)) {
                        echo json_encode(["error" => "Periodo no válido. Debe ser ANUAL, MENSUAL o SEMANAL."]);
                        exit;
                    }

                    $dataGrafico = $contactabilidadModel->getGraficoContactabilidad([
  'periodo'    => $periodo,
  'fechaDesde' => $fechaDesde,
  'fechaHasta' => $fechaHasta
]);

                    echo json_encode($dataGrafico);
                    break;

                default:
                    echo json_encode(["error" => "Operación no válida"]);
                    break;
            }
            break;

        default:
            echo json_encode(["error" => "Método no soportado"]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
