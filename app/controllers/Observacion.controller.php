<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Observacion.php";
    require_once "../helpers/helper.php";

    $observacion = new Observacion();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['task']) && $_GET['task'] === 'getObservacionByOrden') {
                $idorden = intval($_GET['idorden'] ?? 0);
                echo json_encode($observacion->getObservacionByOrden($idorden));
            }
            break;

        case 'POST':
            // _idcomponente, _idorden y estado vienen por $_POST
            $registro = [
                "idcomponente" => intval($_POST["idcomponente"] ?? 0),
                "idorden"      => intval($_POST["idorden"]      ?? 0),
                "estado"       => isset($_POST["estado"]) ? 1 : 0,
                "foto"         => ""
            ];

            // Validar básicos
            if (!$registro["idcomponente"] || !$registro["idorden"]) {
                echo json_encode(["error" => "Faltan componente u orden"]);
                exit;
            }

            // Manejo del archivo
            if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
                $tmpPath   = $_FILES["foto"]["tmp_name"];
                $origName  = $_FILES["foto"]["name"];
                $ext       = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                $allowed   = ["jpg","jpeg","png"];
                if (!in_array($ext, $allowed)) {
                    echo json_encode(["error" => "Extensión no permitida"]);
                    exit;
                }

                // Nombre único
                $newName     = md5(time() . $origName) . '.' . $ext;
                $uploadDir   = __DIR__ . "/../../images/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $destinyPath = $uploadDir . $newName;

                if (!move_uploaded_file($tmpPath, $destinyPath)) {
                    echo json_encode(["error" => "Error al mover el archivo."]);
                    exit;
                }
                // ruta relativa para BD / vistas
                $registro["foto"] = "images/" . $newName;
            }

            // Insertar en BD
            $n = $observacion->add($registro);
            if ($n > 0) {
                echo json_encode(["success" => "Observación registrada", "rows" => $n]);
            } else {
                echo json_encode(["error" => "No se pudo registrar la observación"]);
            }
            break;

        default:
            // Opcional: 405 Method Not Allowed
            http_response_code(405);
            break;
    }
}
