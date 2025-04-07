

<?php
// Datos
$token = 'apis-token-14104.g67iElklbuoH8v51T7cOgcVhrMLGicKv';
$dni = '71775587';

// Iniciar llamada a API
$curl = curl_init();

// Configurar opciones de cURL
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

// Ejecutar la solicitud y obtener la respuesta
$response = curl_exec($curl);
curl_close($curl);

// Procesar la respuesta
$persona = json_decode($response);
var_dump($persona);
?>
