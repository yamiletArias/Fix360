<?php

require_once "../models/Kardex.php";
header('Content-Type: application/json');

$kardex = new Kardex();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $kardex->add([
                    "idproducto" => Conexion::limpiarCadena($_POST["idproducto"]),
                    "fecha" => Conexion::limpiarCadena($_POST["fecha"]),
                    "stockmin" => Conexion::limpiarCadena($_POST["stockmin"]),
                    "stockmax" => Conexion::limpiarCadena($_POST["stockmax"])
                ]);
                echo json_encode($result);
                break;

            case "update":
                $result = $kardex->update([
                    "idkardex" => Conexion::limpiarCadena($_POST["idkardex"]),
                    "idproducto" => Conexion::limpiarCadena($_POST["idproducto"]),
                    "fecha" => Conexion::limpiarCadena($_POST["fecha"]),
                    "stockmin" => Conexion::limpiarCadena($_POST["stockmin"]),
                    "stockmax" => Conexion::limpiarCadena($_POST["stockmax"])
                ]);
                echo json_encode($result);
                break;

            case "delete":
                $idkardex = Conexion::limpiarCadena($_POST["idkardex"]);
                echo json_encode($kardex->delete($idkardex));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idproducto"])) {
            $idproducto = Conexion::limpiarCadena($_GET["idproducto"]);
            echo json_encode($kardex->find($idproducto));
        } else {
            echo json_encode($kardex->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
