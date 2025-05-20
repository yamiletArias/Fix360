<?php

require_once "../models/Empresa.php";
require_once "../helpers/helper.php";
header('Content-Type: application/json');


$empresa = new Empresa();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $empresa->add([
                    "nomcomercial" => Helper::limpiarCadena($_POST["nomcomercial"]),
                    "razonsocial" => Helper::limpiarCadena($_POST["razonsocial"]),
                    "telefono" => Helper::limpiarCadena($_POST["telefono"]),
                    "correo" => Helper::limpiarCadena($_POST["correo"]),
                    "ruc" => Helper::limpiarCadena($_POST["ruc"])
                ]);
                echo json_encode($result);
                break;

            case "update":
                $result = $empresa->update([
                    "idempresa" => Helper::limpiarCadena($_POST["idempresa"]),
                    "nomcomercial" => Helper::limpiarCadena($_POST["nomcomercial"]),
                    "razonsocial" => Helper::limpiarCadena($_POST["razonsocial"]),
                    "telefono" => Helper::limpiarCadena($_POST["telefono"]),
                    "correo" => Helper::limpiarCadena($_POST["correo"]),
                    "ruc" => Helper::limpiarCadena($_POST["ruc"])
                ]);
                echo json_encode($result);
                break;

            case "delete":
                $idempresa = Helper::limpiarCadena($_POST["idempresa"]);
                echo json_encode($empresa->delete($idempresa));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET['task']) && $_GET['task'] === 'getAll') {
            echo json_encode($empresa->getAll());
        } elseif (isset($_GET['task']) && $_GET['task'] === 'getById') {
            $id = intval($_GET['idempresa'] ?? 0);
            if ($id > 0) {
                echo json_encode($empresa->GetById($id));
            } else {
                echo json_encode(['status' => false, 'message' => 'ID inválido']);
            }
        }
        break;


    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
