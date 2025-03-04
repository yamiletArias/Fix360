<?php

require_once "../models/Subcategoria.php";
header('Content-Type: application/json');

$subcategoria = new Subcategoria();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $subcategoria->add([
                    "idcategoria" => Conexion::limpiarCadena($_POST["idcategoria"]),
                    "subcategoria" => Conexion::limpiarCadena($_POST["subcategoria"])
                ]);
                echo json_encode($result);
                break;

            case "update":
                $result = $subcategoria->update([
                    "idsubcategoria" => Conexion::limpiarCadena($_POST["idsubcategoria"]),
                    "idcategoria" => Conexion::limpiarCadena($_POST["idcategoria"]),
                    "subcategoria" => Conexion::limpiarCadena($_POST["subcategoria"])
                ]);
                echo json_encode($result);
                break;

            case "delete":
                $idsubcategoria = Conexion::limpiarCadena($_POST["idsubcategoria"]);
                echo json_encode($subcategoria->delete($idsubcategoria));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idsubcategoria"])) {
            $idsubcategoria = Conexion::limpiarCadena($_GET["idsubcategoria"]);
            echo json_encode($subcategoria->find($idsubcategoria));
        } else {
            echo json_encode($subcategoria->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
