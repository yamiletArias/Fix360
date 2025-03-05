<?php

require_once "../models/Producto.php";
header('Content-Type: application/json');

$producto = new Producto();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if (!isset($_POST["operation"])) {
            echo json_encode(["status" => false, "message" => "Operación no especificada"]);
            exit;
        }

        switch ($_POST["operation"]) {
            case "register":
                echo json_encode($producto->add($_POST));
                break;

            case "update":
                echo json_encode($producto->update($_POST));
                break;

            case "delete":
                echo json_encode($producto->delete($_POST["idproducto"]));
                break;

            default:
                echo json_encode(["status" => false, "message" => "Operación no válida"]);
        }
        break;

    case "GET":
        if (isset($_GET["idproducto"])) {
            echo json_encode($producto->find($_GET["idproducto"]));
        } else {
            echo json_encode($producto->getAll());
        }
        break;
    
    default:
        echo json_encode(["status" => false, "message" => "Método no permitido"]);
}
