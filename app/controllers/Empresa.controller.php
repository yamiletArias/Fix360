<?php

require_once "../models/EmpresaModel.php";
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
                    "nomcomercial" => Conexion::limpiarCadena($_POST["nomcomercial"]),
                    "razonsocial" => Conexion::limpiarCadena($_POST["razonsocial"]),
                    "telefono" => Conexion::limpiarCadena($_POST["telefono"]),
                    "correo" => Conexion::limpiarCadena($_POST["correo"]),
                    "ruc" => Conexion::limpiarCadena($_POST["ruc"])
                ]);
                echo json_encode($result);
                break;

            case "update":
                $result = $empresa->update([
                    "idempresa" => Conexion::limpiarCadena($_POST["idempresa"]),
                    "nomcomercial" => Conexion::limpiarCadena($_POST["nomcomercial"]),
                    "razonsocial" => Conexion::limpiarCadena($_POST["razonsocial"]),
                    "telefono" => Conexion::limpiarCadena($_POST["telefono"]),
                    "correo" => Conexion::limpiarCadena($_POST["correo"]),
                    "ruc" => Conexion::limpiarCadena($_POST["ruc"])
                ]);
                echo json_encode($result);
                break;

            case "delete":
                $idempresa = Conexion::limpiarCadena($_POST["idempresa"]);
                echo json_encode($empresa->delete($idempresa));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["ruc"])) {
            $ruc = Conexion::limpiarCadena($_GET["ruc"]);
            echo json_encode($empresa->find($ruc));
        } else {
            echo json_encode($empresa->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
