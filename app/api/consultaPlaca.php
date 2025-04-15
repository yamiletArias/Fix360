<?php
// consultaPlaca.php
header('Content-Type: application/json');

if (!isset($_GET['placa'])) {
    echo json_encode(['error' => 'Placa no proporcionada']);
    exit;
}

$token = 'RZJ8gztQYg2SJR3w8FOmj9D9bihQanc2umnUWyvvDJMPAodEyIk7hFNzOk1k';
$placa = strtoupper($_GET['placa']); // Convertir a mayúsculas, si es necesario

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.peruapis.com/vehiculos/placa?numero=' . urlencode($placa),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 2,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer ' . $token,
    'Accept: application/json'

  ),
));

$response = curl_exec($curl);
curl_close($curl);

echo $response;
?>
