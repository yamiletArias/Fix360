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
            if ($_GET['task'] === 'find' && isset($_GET['idproducto'])) {
                echo json_encode($producto->find(intval($_GET['idproducto'])));
            }
            break;


        case 'POST':
            if (isset($_POST['action']) && $_POST['action'] === 'update') {
                // --- Bloque de actualización ---
                $registro = [
                    'idproducto'   => intval($_POST['idproducto'] ?? 0),
                    'descripcion'  => Helper::limpiarCadena($_POST['descripcion'] ?? ''),
                    'cantidad'     => floatval($_POST['cantidad'] ?? 0),
                    'precioc'       => floatval($_POST['precioc'] ?? 0),
                    'preciov'       => floatval($_POST['preciov'] ?? 0),
                    'img'          => '',
                    "codigobarra"   => Helper::limpiarCadena($_POST["codigobarra"] ?? ''),  // luego asignamos si hay archivo
                    'stockmin'     => intval($_POST['stockmin'] ?? 0),
                    'stockmax'     => ($_POST['stockmax'] !== ''
                        ? intval($_POST['stockmax'])
                        : null)
                ];

                // Validaciones básicas
                if ($registro['idproducto'] <= 0) {
                    echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
                    exit;
                }
                if (
                    $registro['stockmin'] < 0
                    || ($registro['stockmax'] !== null
                        && $registro['stockmax'] < $registro['stockmin'])
                ) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Stock mínimo ≥0 y máximo ≥ mínimo'
                    ]);
                    exit;
                }

                // Manejo de la imagen (opcional)
                if (
                    !empty($_FILES['img']['name'])
                    && $_FILES['img']['error'] === UPLOAD_ERR_OK
                ) {

                    // 1) Determinar extensión y nombre final
                    $ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
                    $nuevoNombre = 'prod_' . time() . '.' . $ext;

                    // 2) Construir la ruta absoluta CORRECTA al directorio “images/”
                    //    __DIR__ = C:\xampp\htdocs\fix360\app\controllers
                    //    '/../../images/' sube dos niveles hasta “fix360” y entra a “images”
                    $destino = __DIR__ . '/../../images/' . $nuevoNombre;

                    // 3) Mover el archivo temporal a esa ruta
                    if (move_uploaded_file($_FILES['img']['tmp_name'], $destino)) {
                        // 4) Guardar la ruta relativa que luego irá a BD (para <img src="...">)
                        $registro['img'] = 'images/' . $nuevoNombre;
                    } else {
                        echo json_encode([
                            'status'  => 'error',
                            'message' => 'No se pudo subir la imagen'
                        ]);
                        exit;
                    }
                }

                // Llamada al modelo
                $ok = $producto->update($registro);
                if ($ok) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Producto actualizado'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al actualizar'
                    ]);
                }
                exit;
            }

            // --- Bloque de inserción (add) ---
            $registro = [
                "idsubcategoria" => intval($_POST["subcategoria"] ?? 0),
                "idmarca"        => intval($_POST["idmarca"]      ?? 0),
                "descripcion"    => Helper::limpiarCadena($_POST["descripcion"] ?? ''),
                "precioc"         => floatval($_POST["precioc"]     ?? 0),
                "preciov"         => floatval($_POST["preciov"]     ?? 0),
                "presentacion"   => Helper::limpiarCadena($_POST["presentacion"] ?? ''),
                "undmedida"      => Helper::limpiarCadena($_POST["undmedida"]    ?? ''),
                "cantidad"       => floatval($_POST["cantidad"]   ?? 0),
                "img"            => "",
                "codigobarra"   => Helper::limpiarCadena($_POST["codigobarra"] ?? ''),     // aquí pondremos la ruta si se sube
                "stockInicial"   => intval($_POST["stockInicial"] ?? 0),
                "stockmin"       => intval($_POST["stockmin"]     ?? 0),
                "stockmax"     => ($_POST["stockmax"] !== '' 
                       ? intval($_POST["stockmax"]) 
                       : null),
            ];

            // validaciones...
           if ($registro['stockmin'] < 0
    || ($registro['stockmax'] !== null 
        && $registro['stockmax'] < $registro['stockmin'])
) {
    echo json_encode(['error'=>'El stock mínimo debe ser ≥ 0 y el máximo ≥ mínimo']);
    exit;
}

            // *** Aquí agregamos el manejo de la imagen ***
            if (!empty($_FILES['img']['name']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
                $nuevoNombre = 'prod_' . time() . '.' . $ext;
                $destino = __DIR__ . '/../../images/' . $nuevoNombre;
                if (move_uploaded_file($_FILES['img']['tmp_name'], $destino)) {
                    $registro['img'] = 'images/' . $nuevoNombre;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'No se pudo subir la imagen']);
                    exit;
                }
            }

            // Insertar en BD
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
            exit;


        default:
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
            break;
    }
}
