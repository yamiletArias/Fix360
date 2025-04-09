<?php

require_once "../models/Persona.php";
require_once "../helpers/helper.php";
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
                    "nombres"           => Helper::limpiarCadena($_POST["nombres"]),
                    "apellidos"         => Helper::limpiarCadena($_POST["apellidos"]),
                    "tipodoc"           => Helper::limpiarCadena($_POST["tipodoc"]),
                    "numdoc"            => Helper::limpiarCadena($_POST["numdoc"]),
                    "direccion"         => Helper::limpiarCadena($_POST["direccion"]),
                    "correo"            => Helper::limpiarCadena($_POST["correo"]),
                    "telprincipal"      => Helper::limpiarCadena($_POST["telprincipal"]),
                    "telalternativo"    => Helper::limpiarCadena($_POST["telalternativo"])
                ]);
                echo json_encode($result);
                break;

            case "update":
                $result = $persona->update([
                    "idpersona"         => Helper::limpiarCadena($_POST["idpersona"]),
                    "nombres"           => Helper::limpiarCadena($_POST["nombres"]),
                    "apellidos"         => Helper::limpiarCadena($_POST["apellidos"]),
                    "tipodoc"           => Helper::limpiarCadena($_POST["tipodoc"]),
                    "numdoc"            => Helper::limpiarCadena($_POST["numdoc"]),
                    "direccion"         => Helper::limpiarCadena($_POST["direccion"]),
                    "correo"            => Helper::limpiarCadena($_POST["correo"]),
                    "telprincipal"      => Helper::limpiarCadena($_POST["telprincipal"]),
                    "telalternativo"    => Helper::limpiarCadena($_POST["telalternativo"])
                ]);
                echo json_encode($result);
                break;

            case "delete":
                $idpersona = Helper::limpiarCadena($_POST["idpersona"]);
                echo json_encode($persona->delete($idpersona));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
       if($_GET['task'] == 'getAll'){
        echo json_encode($persona->getAll());
       }
       if($_GET['task'] == 'getById'){
        echo json_encode($persona->GetById($idpersona));
       }
       break;

}
