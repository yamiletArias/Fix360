<?php
// consulta-dni.php

header('Content-Type: application/json');

if (!isset($_GET['dni'])) {
    echo json_encode(['error' => 'DNI no proporcionado']);
    exit;
}

$token = 'apis-token-14104.g67iElklbuoH8v51T7cOgcVhrMLGicKv';
$dni = $_GET['dni'];

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 2,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Referer: https://apis.net.pe/consulta-dni-api',
    'Authorization: Bearer ' . $token
  ),
));

$response = curl_exec($curl);
curl_close($curl);

// Retornamos directamente la respuesta (en JSON)
echo $response;
?>
