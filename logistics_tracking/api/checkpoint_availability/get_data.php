<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://idgbthb2a1vw003/scm_delivery_monitoring/api/checkpoint_availability/',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic dXNlcjp1c2Vy',
    'Content-Type: application/json'
  ),
));

$location_lot_values = array();

$response = curl_exec($curl);
echo $response; 
$locationlot = json_decode($response,true);

$location_lot_values = array();

  for ($i = 0; $i<=5; $i++){
    $location_name = $locationlot['data'][$i]['locationName'];
    $lotTotal = $locationlot['data'][$i]['IotTotal'];
    array_push( $location_lot_values, [$location_name,$lotTotal] ); 
  }
  echo json_encode($location_lot_values);      
?>
