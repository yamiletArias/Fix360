<?php

require_once "../models/Categoria.php";
header('Content-Type: application/json');

$categoria = new Categoria();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $categoria->add([
                    "categoria" => Conexion::limpiarCadena($_POST["categoria"])
                ]);
                echo json_encode($result);
                break;

            case "update":
                $result = $categoria->update([
                    "idcategoria" => Conexion::limpiarCadena($_POST["idcategoria"]),
                    "categoria" => Conexion::limpiarCadena($_POST["categoria"])
                ]);
                echo json_encode($result);
                break;

            case "delete":
                $idcategoria = Conexion::limpiarCadena($_POST["idcategoria"]);
                echo json_encode($categoria->delete($idcategoria));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idcategoria"])) {
            $idcategoria = Conexion::limpiarCadena($_GET["idcategoria"]);
            echo json_encode($categoria->find($idcategoria));
        } else {
            echo json_encode($categoria->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
