<?php
// permiso.php
// Ubicación: C:\xampp\htdocs\fix360\app\models\permiso.php

// NOTA: NO volvemos a hacer session_start() aquí, ya lo hizo header.php

// 1) Verificar que el usuario esté logueado
if (
    ! isset($_SESSION['login']) ||
    ! isset($_SESSION['login']['status']) ||
    $_SESSION['login']['status'] !== true
) {
    // Redirigir al login (o a SERVERURL) si no hay sesión
    header('Location: ' . SERVERURL);
    exit;
}

// 2) Obtener el rol guardado en sesión
$idrol = intval($_SESSION['login']['idrol'] ?? 0);
if ($idrol <= 0) {
    // Rol inválido → forzar logout o denegar
    session_destroy();
    header('Location: ' . SERVERURL);
    exit;
}

// 3) Determinar la vista actual (sin parámetros en la URL)
$currentFile = basename($_SERVER['PHP_SELF']);
// Ejemplo: si acceso "listar-colaborador.php?id=5", $currentFile = "listar-colaborador.php"

// 4) Conectar a la base de datos
require_once __DIR__ . '/Conexion.php';

$pdo = (new Conexion())->getConexion();

// 5) Buscar en `vistas` el idvista que corresponda a $currentFile
$stmt = $pdo->prepare("
    SELECT idvista
      FROM vistas
     WHERE ruta = :ruta
    LIMIT 1
");
$stmt->execute([':ruta' => $currentFile]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (! $row) {
    // Si la vista no está registrada en la tabla, devolvemos 404
    header('Location: ' . SERVERURL . 'views/pages/samples/error-404.php');
    exit("404: Vista no registrada.");
}

$idvista = intval($row['idvista']);

// 6) Verificar en `rolvistas` si (idrol, idvista) existe
$stmt2 = $pdo->prepare("
    SELECT 1
      FROM rolvistas
     WHERE idrol   = :idrol
       AND idvista = :idvista
     LIMIT 1
");
$stmt2->execute([
    ':idrol'   => $idrol,
    ':idvista' => $idvista
]);
$ok = $stmt2->fetchColumn();

if (! $ok) {
    // 403 Forbidden si el rol no tiene permiso
    header('Location: ' . SERVERURL . 'views/pages/samples/error-500.php');
    exit("403: Acceso denegado.");
}

// Si llegamos aquí, el usuario tiene permiso para esta vista y el script continúa.
