<?php

require_once "../models/Personas.php";
header('Content-Type: application/json');

$persona = new Persona();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                $result = $persona->add([
                    "nombres" => Conexion::limpiarCadena($_POST["nombres"]),
                    "apellidos" => Conexion::limpiarCadena($_POST["apellidos"]),
                    "tipodoc" => Conexion::limpiarCadena($_POST["tipodoc"]),
                    "numdoc" => Conexion::limpiarCadena($_POST["numdoc"]),
                    "direccion" => Conexion::limpiarCadena($_POST["direccion"]),
                    "correo" => Conexion::limpiarCadena($_POST["correo"]),
                    "telefono" => Conexion::limpiarCadena($_POST["telefono"])
                ]);
                echo json_encode($result);
                break;

            case "update":
                $result = $persona->update([
                    "idpersona" => Conexion::limpiarCadena($_POST["idpersona"]),
                    "nombres" => Conexion::limpiarCadena($_POST["nombres"]),
                    "apellidos" => Conexion::limpiarCadena($_POST["apellidos"]),
                    "tipodoc" => Conexion::limpiarCadena($_POST["tipodoc"]),
                    "numdoc" => Conexion::limpiarCadena($_POST["numdoc"]),
                    "direccion" => Conexion::limpiarCadena($_POST["direccion"]),
                    "correo" => Conexion::limpiarCadena($_POST["correo"]),
                    "telefono" => Conexion::limpiarCadena($_POST["telefono"])
                ]);
                echo json_encode($result);
                break;

            case "delete":
                $idpersona = Conexion::limpiarCadena($_POST["idpersona"]);
                echo json_encode($persona->delete($idpersona));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["numdoc"])) {
            $numdoc = Conexion::limpiarCadena($_GET["numdoc"]);
            echo json_encode($persona->find($numdoc));
        } else {
            echo json_encode($persona->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}