<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Producto.php";
    require_once "../helpers/helper.php";

    $producto = new Producto();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($_GET['task'] == 'getAll') {
                echo json_encode($producto->getAll());
            }
            break;

        case 'POST':
            // Para formularios enviados como multipart/form-data
            // se utilizan $_POST y $_FILES en lugar de leer un JSON.
            $registro = [
                "idsubcategoria" => Helper::limpiarCadena($_POST["subcategoria"] ?? ""),  // Asegúrate de que el name del input coincida, aquí "subcategoria"
                "idmarca" => Helper::limpiarCadena($_POST["idmarca"] ?? ""),
                "descripcion" => Helper::limpiarCadena($_POST["descripcion"] ?? ""),
                "precio" => Helper::limpiarCadena($_POST["precio"] ?? ""),
                "presentacion" => Helper::limpiarCadena($_POST["presentacion"] ?? ""),
                "undmedida" => Helper::limpiarCadena($_POST["undmedida"] ?? ""),
                "cantidad" => Helper::limpiarCadena($_POST["cantidad"] ?? ""),
                "img" => ""
            ];
            // Manejo del archivo
            if (isset($_FILES["img"]) && $_FILES["img"]["error"] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES["img"]["tmp_name"];
                $fileName = $_FILES["img"]["name"];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $allowedfileExtensions = array("jpg", "jpeg", "png");
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    // Generar un nombre único para el archivo
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    // Carpeta destino relativa al proyecto
                    $uploadFileDir = __DIR__ . "/../../images/";
                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $registro["img"] = "images/" . $newFileName;
                    } else {
                        echo json_encode(["error" => "Error al mover el archivo."]);
                        exit;
                    }
                } else {
                    echo json_encode(["error" => "Extensión no permitida."]);
                    exit;
                }
            } else {
                $registro["img"] = "";
            }
            $n = $producto->add($registro);
            if ($n > 0) {
                echo json_encode(["success" => "Producto registrado", "rows" => 1, "idproducto" => $n]);
            } else {
                echo json_encode(["error" => "No se pudo registrar el producto"]);
            }
            break;

        default:
            break;
    }
}
?>