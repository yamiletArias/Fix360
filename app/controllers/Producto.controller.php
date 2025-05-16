<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../helpers/helper.php';


    $producto = new Producto();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($_GET['task'] == 'getAll') {
                echo json_encode($producto->getAll());
            }
            break;

         case 'POST':
        // Campos del formulario multipart/form-data
        $registro = [
            "idsubcategoria" => intval($_POST["subcategoria"] ?? 0),
            "idmarca"        => intval($_POST["idmarca"]      ?? 0),
            "descripcion"    => Helper::limpiarCadena($_POST["descripcion"] ?? ''),
            "precio"         => floatval($_POST["precio"]     ?? 0),
            "presentacion"   => Helper::limpiarCadena($_POST["presentacion"] ?? ''),
            "undmedida"      => Helper::limpiarCadena($_POST["undmedida"]    ?? ''),
            "cantidad"       => floatval($_POST["cantidad"]   ?? 0),
            "img"            => "",
            "stockmin"       => intval($_POST["stockmin"]     ?? 0),
            "stockmax"       => intval($_POST["stockmax"]     ?? 0),
        ];
        if ($registro['stockmin'] < 0 || $registro['stockmax'] < $registro['stockmin']) {
    echo json_encode(['error' => 'El stock mínimo debe ser ≥ 0 y el máximo ≥ mínimo']);
    exit;
}

        // Manejo de la imagen (igual que antes)...
        if (isset($_FILES["img"]) && $_FILES["img"]["error"] === UPLOAD_ERR_OK) {
            // ... tu lógica de mover y asignar $registro["img"] ...
        }

        // Llamada al modelo
        $newId = $producto->add($registro);
        if ($newId > 0) {
            echo json_encode([
                "success"    => "Producto registrado",
                "rows"       => 1,
                "idproducto" => $newId
            ]);
        } else {
            echo json_encode(["error" => "No se pudo registrar el producto"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
    }
}
?>