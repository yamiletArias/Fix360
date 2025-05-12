<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Observacion.php";
    require_once "../helpers/helper.php";

    $observacion = new Observacion();
    $task        = $_GET['task'] ?? '';

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($task === 'getObservacionByOrden') {
                $idorden = intval($_GET['idorden'] ?? 0);
                echo json_encode($observacion->getObservacionByOrden($idorden));
            }
            elseif ($task === 'deleteObservacion') {
                $idobs = intval($_GET['idobservacion'] ?? 0);
                if (!$idobs) {
                    http_response_code(400);
                    echo json_encode(['error'=>'Falta idobservacion']);
                    exit;
                }

                // 1) Recuperar y borrar la imagen existente
                $viejo      = $observacion->find($idobs);
                $rutaVieja  = $viejo['foto'] ?? '';
                if ($rutaVieja && file_exists(__DIR__ . '/../../' . $rutaVieja)) {
                    unlink(__DIR__ . '/../../' . $rutaVieja);
                }

                // 2) Borrar registro de la BD
                $observacion->delete($idobs);

                echo json_encode(['success'=>'Observación eliminada']);
            }
            break;

        case 'POST':
            if ($task === 'add') {
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
            }
            elseif ($task === 'updateObservacion') {
                // 1) Recoger parámetros básicos
                $registro = [
                    'idobservacion' => intval($_POST['idobservacion'] ?? 0),
                    'idcomponente'  => intval($_POST['idcomponente']  ?? 0),
                    'estado'        => isset($_POST['estado']) ? 1 : 0,
                    // por defecto, la ruta que llega oculta
                    'foto'          => $_POST['oldFoto'] ?? ''
                ];
                if (!$registro['idobservacion'] || !$registro['idcomponente']) {
                    http_response_code(400);
                    echo json_encode(['error'=>'Falta idobservacion o idcomponente']);
                    exit;
                }

                // 2) Si se sube fichero nuevo, reemplazo la imagen antigua
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    // 2.1) Borro la antigua
                    if ($registro['foto'] && file_exists(__DIR__ . '/../../' . $registro['foto'])) {
                        unlink(__DIR__ . '/../../' . $registro['foto']);
                    }

                    // 2.2) Subo la nueva
                    $tmp     = $_FILES['foto']['tmp_name'];
                    $orig    = $_FILES['foto']['name'];
                    $ext     = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                    $allow   = ['jpg','jpeg','png'];
                    if (!in_array($ext, $allow)) {
                        http_response_code(415);
                        echo json_encode(['error'=>'Extensión no permitida']);
                        exit;
                    }
                    $newName   = md5(time().$orig).'.'.$ext;
                    $uploadDir = __DIR__ . '/../../images/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir,0755,true);
                    $dest = $uploadDir . $newName;
                    if (!move_uploaded_file($tmp, $dest)) {
                        http_response_code(500);
                        echo json_encode(['error'=>'Error moviendo archivo']);
                        exit;
                    }
                    // 2.3) Actualizo la ruta en el registro
                    $registro['foto'] = 'images/' . $newName;
                }

                // 3) Llamar al modelo
                $n = $observacion->update($registro);
                if ($n > 0) {
                    echo json_encode(['success'=>'Observación actualizada', 'rows'=>$n]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error'=>'No se pudo actualizar']);
                }
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error'=>'Método no permitido']);
            break;
    }
}
